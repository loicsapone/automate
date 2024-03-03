<?php

namespace Automate;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

readonly class Configuration implements ConfigurationInterface
{
    public function __construct(
        private PluginManager $pluginManager,
    ) {
    }

    
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('automate');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('repository')
                    ->isRequired()
                ->end()
                ->arrayNode('shared_files')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('shared_folders')
                    ->scalarPrototype()->end()
                ->end()
                ->append($this->addCommandsNode('pre_deploy'))
                ->append($this->addCommandsNode('on_deploy'))
                ->append($this->addCommandsNode('post_deploy'))
                ->append($this->addPlatformsNode())
                ->append($this->addPluginsNode())
            ->end();

        return $treeBuilder;
    }

    private function addCommandsNode(string $name): NodeDefinition
    {
        $treeBuilder = new TreeBuilder($name);

        return $treeBuilder->getRootNode()
            ->arrayPrototype()
                ->beforeNormalization()
                    ->ifString()
                    ->then(static fn($v): array => ['cmd' => $v])
                ->end()
                ->children()
                    ->scalarNode('cmd')->isRequired()->cannotBeEmpty()->end()
                    ->arrayNode('only')
                        ->defaultValue([])
                        ->beforeNormalization()
                        ->ifString()
                            ->then(static fn($v): array => [$v])
                        ->end()
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addPlatformsNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('platforms');

        return $treeBuilder->getRootNode()
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->normalizeKeys(false)
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->children()
                    ->scalarNode('default_branch')->isRequired()->cannotBeEmpty()->end()
                    ->integerNode('max_releases')->end()
                    ->append($this->addServersNode())
                ->end()
            ->end();
    }

    private function addServersNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('servers');

        return $treeBuilder->getRootNode()
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->normalizeKeys(false)
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->children()
                    ->scalarNode('host')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('user')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('password')->end()
                    ->scalarNode('ssh_key')->cannotBeEmpty()->end()
                    ->scalarNode('path')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('shared_path')->cannotBeEmpty()->end()
                    ->integerNode('port')->end()
                ->end()
            ->end();
    }

    private function addPluginsNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('plugins');

        $node = $treeBuilder->getRootNode()->children();

        foreach ($this->pluginManager->getPlugins() as $plugin) {
            $node->append($plugin->getConfigurationNode());
        }

        return $node->end();
    }
}
