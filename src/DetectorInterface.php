<?php


	namespace LiftKit\EnvironmentDetection;


	interface DetectorInterface
	{


		/**
		 * This method tests the $pattern against value in $_SERVER['HTTP_HOST'].
		 *
		 * @param string $pattern Supports * wildcards
		 * @param string $environment A string to represent the matched environment
		 *
		 * @return self
		 */
		public function ifHttpHost ($pattern, $environment);


		/**
		 * This method tests the $pattern against php_uname('n') (i.e. `uname -n` in a shell)
		 *
		 * @param string $pattern Supports * wildcards
		 * @param string $environment A string to represent the matched environment
		 *
		 * @return self
		 */
		public function ifHostName ($pattern, $environment);


		/**
		 * This method tests the $pattern against value in $_ENV
		 *
		 * @param string $env Key of the variable in $_ENV
		 * @param string $pattern Supports * wildcards
		 * @param string $environment A string to represent the matched environment
		 *
		 * @return self
		 */
		public function ifEnv ($env, $pattern, $environment);


		/**
		 * This method tests the $pattern against the parameter $value directly
		 *
		 * @param string $env Key of the variable in $_ENV
		 * @param string $pattern Supports * wildcards
		 * @param string $environment A string to represent the matched environment
		 *
		 * @return self
		 */
		public function ifMatch ($value, $pattern, $environment);


		/**
		 * If
		 *
		 * @param bool $bool If true, this will detect the environment as the string $environment
		 * @param string $environment A string to represent the matched environment
		 *
		 * @return self
		 */
		public function ifBool ($bool, $environment);


		/**
		 * Returns the first matched environment string, testing each condition in the order they were defined.
		 *
		 * @return string|null Returns null if no match is found
		 */
		public function resolve ();


		/**
		 * Clears any previously called environment conditions
		 */
		public function clear ();


		/**
		 * Matches $value against $pattern
		 *
		 * @param string $value String value to match against
		 * @param string $pattern Supports * wildcards
		 * @param bool $insensitive If true, match is case insensitive
		 *
		 * @return boolean
		 */
		public function match ($value, $pattern, $insensitive = false);
	}