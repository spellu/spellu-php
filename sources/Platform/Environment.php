<?php

namespace Spellu\Platform;

use Composer\Autoload\ClassLoader;

/**
 * PHP言語環境
 */
class Environment
{
	protected $composer;
	protected $packages;
	protected $symbols;

	public static function toPhpSymbol($fullname)
	{
		return str_replace('::', '\\', $fullname);
	}

	public function __construct(ClassLoader $composer, $vendor_directory_path)
	{
		$this->composer = $composer;
		$this->packages = $this->loadPackages($vendor_directory_path);
		$this->symbols = $this->loadSymbols();
	}

	protected function loadPackages($base_path)
	{
		$packages = [];

		// vendor下をディレクトリサーチする
		$paths = glob($base_path.'/*/*/composer.json');

		foreach ($paths as $path) {
			list($vendor, $package) = explode('/', substr($path, strlen($base_path) + 1));

			$package = new Package($base_path, $vendor, $package);
			$packages[$package->packageName()] = $package;
		}

		return $packages;
	}

	protected function loadSymbols()
	{
		// 1. PHPランタイムにロード済みのもの

		// 2. Composer定義 (psr-0, psr-4, classmap)
		$package_symbols = array_reduce($this->packages, function ($result, $package) {
			$result = array_merge($result, $package->namespaces());
			return $result;
		}, []);

		return array_merge([], $package_symbols);
	}

	public function packages()
	{
		return $this->packages;
	}

	public function package($name)
	{
		return $this->packages[$name] ?? null;
	}

	public function symbols()
	{
		return $this->symbols;
	}

	public function symbol($name)
	{
		return $this->symbols[$name] ?? null;
	}

	public function asNamespace($name)
	{
		foreach ($this->symbols as $fullname => $symbol) {
			// 完全一致
			if ($fullname === $name) {
				return $symbol;
			}

			// 前方一致
            if (mb_strpos($fullname, $name.'::') === 0) {
				return new ReflectionNamespace($name);
            }

            // 包含
            if (mb_strpos($name, $fullname.'::') === 0) {
				return new ReflectionNamespace($name);
            }
		}

		return null;
	}

	public function asConstant($name)
	{
		$php_name = static::toPhpSymbol($name);

		if (! defined($php_name)) return null;

		return new ReflectionConstant($name, constant($php_name));
	}

	public function asFunction($name)
	{
		try {
			$reflection = new \ReflectionFunction(static::toPhpSymbol($name));
			return new ReflectionFunction($name, $reflection);
		}
		catch (\ReflectionException $e) {
			return null;
		}
	}

	public function asClass($name)
	{
		try {
			$reflection = new \ReflectionClass(static::toPhpSymbol($name));
			return new ReflectionClass($name, $reflection);
		}
		catch (\ReflectionException $e) {
			return null;
		}
	}
}
