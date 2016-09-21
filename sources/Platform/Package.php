<?php

namespace Spellu\Platform;

class Package
{
	protected $config;

	protected $namespaces;	// [namespace => (class | interface | trait)]

	protected $classes;

	public function __construct($base_path, $vendor, $package)
	{
		$this->base_path = realpath($base_path);
		$this->vendor = $vendor;
		$this->package = $package;
		$this->config = json_decode(file_get_contents($this->packagePath('composer.json')), true);
		$this->namespaces = [];
		$this->classes = [];

		$this->loadNamespaces();
	}

	public function packageName()
	{
		return "{$this->vendor}/{$this->package}";
	}

	public function packagePath($path = null)
	{
		if (! $path)
			return "{$this->base_path}/{$this->packageName()}";
		else
			return $this->packagePath().'/'.$path;
	}

	public function namespaces()
	{
		return $this->namespaces;
	}

	protected function loadNamespaces()
	{
		$this->loadPsr0($this->config['autoload']['psr-0'] ?? []);
//		$this->loadPsr0($this->config['autoload-dev']['psr-0'] ?? []);
		$this->loadPsr4($this->config['autoload']['psr-4'] ?? []);
//		$this->loadPsr4($this->config['autoload-dev']['psr-4'] ?? []);
		// TODO classmap
	}

	protected function loadPsr0($mapping)
	{
        // "Spellu\\": "sources/"
		foreach ($mapping as $namespace => $paths) {
			$fullname = str_replace('\\', '::', rtrim($namespace, '\\'));
			$this->namespaces[$fullname] = new ReflectionNamespace($fullname);
		}
	}

	protected function loadPsr4($mapping)
	{
        // "Spellu\\": "sources/"
		foreach ($mapping as $namespace => $paths) {
			$fullname = str_replace('\\', '::', rtrim($namespace, '\\'));
			$this->namespaces[$fullname] = new ReflectionNamespace($fullname);
		}
	}
}
