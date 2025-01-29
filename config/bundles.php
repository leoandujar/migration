<?php

return [
	Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
	Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
	Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
	Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
	Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
	Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
	Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
	Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true, 'test' => true],
	Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
	Snc\RedisBundle\SncRedisBundle::class => ['all' => true],
	JMS\SerializerBundle\JMSSerializerBundle::class => ['all' => true],
	Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle::class => ['all' => true],
	KnpU\OAuth2ClientBundle\KnpUOAuth2ClientBundle::class => ['all' => true],
	Symfony\Bundle\MercureBundle\MercureBundle::class => ['all' => true],
	Sentry\SentryBundle\SentryBundle::class => ['prod' => true],
	League\FlysystemBundle\FlysystemBundle::class => ['all' => true],
	Zenstruck\Messenger\Monitor\ZenstruckMessengerMonitorBundle::class => ['all' => true],
];
