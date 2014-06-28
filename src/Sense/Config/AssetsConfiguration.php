<?php
/**
 * Created by PhpStorm.
 * User: Asier
 * Date: 28/06/14
 * Time: 14:59
 */

namespace Sense\Config;


use Sense\Sense;
use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class AssetsConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();


        $rootNode    =  $treeBuilder->root('config');
        /**
         * @var $assets_node \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
         */

        $assets_node =  $rootNode->children()->arrayNode('assets')->isRequired()->addDefaultsIfNotSet();

        /**
         * @var $assets_node_children \Symfony\Component\Config\Definition\Builder\NodeBuilder
         */
        $assets_node_children =  $assets_node->children();



        foreach (array("theme", "admin") as $node_name) {
            $assets_node_children
                ->arrayNode($node_name)
                    ->children()
                        ->arrayNode('scripts')
                            ->useAttributeAsKey('handle')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('file')->defaultNull()->end()
                                    ->enumNode('base_url')
                                        ->values(array("%wp.template_uri%", "%wp.plugin_uri%"))
                                        ->defaultValue("%wp.template_uri%")
                                    ->end()
                                    ->booleanNode('in_footer')->defaultTrue()->end()
                                    ->arrayNode('dependencies')
                                        ->treatNullLike(array())
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('styles')
                            ->useAttributeAsKey('handle')
                            ->prototype('array')
                            ->children()
                                ->scalarNode('file')->defaultNull()->end()
                                ->enumNode('base_url')
                                    ->values(array("%wp.template_uri%", "%wp.plugin_uri%"))
                                    ->defaultValue("%wp.template_uri%")
                                ->end()
                                ->arrayNode('dependencies')
                                    ->treatNullLike(array())
                                    ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end();
        }
        $assets_node_children->end();
        $assets_node->end();

        return $treeBuilder;
    }
    
    function setAssets(array $config, Sense $sense){
        $theme_assets = $config["assets"]["theme"];

        foreach($theme_assets["scripts"] as $handle=>$params){
            /**
             * @var $sense["sense.theme_assets"] AssetManager
             */
            $sense["sense.theme_assets"]->addScript(
                $handle,
                $sense["%wp.template_uri%"] . $params["file"],
                1,
                true,
                $dependencies=$params["dependencies"]
            );
        }

        foreach($theme_assets["styles"] as $handle=>$params){
            /**
             * @var $sense["sense.theme_assets"] AssetManager
             */
            $sense["sense.theme_assets"]->addStyle(
                $handle,
                $sense["%wp.template_uri%"] . $params["file"]
            );
        }
    }
}