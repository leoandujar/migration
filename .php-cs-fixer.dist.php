<?php
	
	$finder = (new PhpCsFixer\Finder())
		->in(__DIR__)
		->exclude('var');
	
	return (new PhpCsFixer\Config())
		->setRules([
			'@Symfony' => true,
			'@PSR12' => true,
			'strict_param' => false,
			'indentation_type' => true,
			'array_syntax' => ['syntax' => 'short'],
		])
		->setIndent("\t")
		->setFinder($finder);
