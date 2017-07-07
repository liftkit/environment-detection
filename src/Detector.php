<?php


	namespace LiftKit\EnvironmentDetection;


	final class Detector implements DetectorInterface
	{
		private $rules = [];
		private $default = null;


		/**
		 * This method tests the $pattern against value in $_SERVER['HTTP_HOST'].
		 *
		 * @param string $pattern     Supports * wildcards
		 * @param string $environment A string to represent the matched environment
		 *
		 * @return self
		 */
		public function ifHttpHost ($pattern, $environment)
		{
			$this->rules[] = function () use ($pattern, $environment) {
				if (! isset($_SERVER)) {
					return null;
				}

				if ($this->match($_SERVER['HTTP_HOST'], $pattern)) {
					return $environment;
				} else {
					return null;
				}
			};

			return $this;
		}


		/**
		 * This method tests the $pattern against php_uname('n') (i.e. `uname -n` in a shell)
		 *
		 * @param string $pattern     Supports * wildcards
		 * @param string $environment A string to represent the matched environment
		 *
		 * @return self
		 */
		public function ifHostName ($pattern, $environment)
		{
			$this->rules[] = function () use ($pattern, $environment) {
				if ($this->match(php_uname('n'), $pattern)) {
					return $environment;
				} else {
					return null;
				}
			};

			return $this;
		}


		/**
		 * This method tests the $pattern against value in $_ENV
		 *
		 * @param string $env         Key of the variable in $_ENV
		 * @param string $pattern     Supports * wildcards
		 * @param string $environment A string to represent the matched environment
		 *
		 * @return self
		 */
		public function ifEnv ($env, $pattern, $environment)
		{
			$this->rules[] = function () use ($env, $pattern, $environment) {
				if (! isset($_ENV[$env])) {
					return null;
				}

				if ($this->match($_ENV[$env], $pattern)) {
					return $environment;
				} else {
					return null;
				}
			};

			return $this;
		}


		/**
		 * This method tests the $pattern against the parameter $value directly
		 *
		 * @param string $env         Key of the variable in $_ENV
		 * @param string $pattern     Supports * wildcards
		 * @param string $environment A string to represent the matched environment
		 *
		 * @return self
		 */
		public function ifMatch ($value, $pattern, $environment)
		{
			$this->rules[] = function () use ($value, $pattern, $environment) {
				if ($this->match($value, $pattern)) {
					return $environment;
				} else {
					return null;
				}
			};

			return $this;
		}


		/**
		 * If
		 *
		 * @param bool   $bool        If true, this will detect the environment as the string $environment
		 * @param string $environment A string to represent the matched environment
		 *
		 * @return self
		 */
		public function ifBool ($bool, $environment)
		{
			$this->rules[] = function () use ($bool, $environment) {
				if ($bool) {
					return $environment;
				} else {
					return null;
				}
			};

			return $this;
		}


		/**
		 * Returns $environment if no other condition is matched
		 *
		 * @param string $environment
		 *
		 * @return self
		 */
		public function defaultTo ($environment)
		{
			$this->default = $environment;

			return $this;
		}


		/**
		 * Returns the first matched environment string, testing each condition in the order they were defined.
		 *
		 * @return string|null Returns null if no match is found
		 */
		public function resolve ()
		{
			foreach ($this->rules as $rule) {
				$environment = $rule();

				if ($environment) {
					return $environment;
				}
			}

			return $this->default;
		}


		/**
		 * Clears any previously called environment conditions and resets default
		 *
		 * @return self
		 */
		public function clear ()
		{
			$this->rules = [];
			$this->default = null;

			return $this;
		}


		/**
		 * @param string $value
		 * @param string $pattern
		 * @param bool   $insensitive
		 *
		 * @return bool
		 */
		public function match ($value, $pattern, $insensitive = false)
		{
			$placeholder = '_____WILDCARD______';
			$delim = '#';

			$pattern = str_replace('*', $placeholder, $pattern);
			$pattern = preg_quote($pattern, $delim);
			$pattern = str_replace($placeholder, '.*', $pattern);
			$pattern = $delim . '^' . $pattern . '$' . $delim;

			if ($insensitive) {
				$pattern .= 'i';
			}

			return (bool) preg_match($pattern, $value);
		}
	}