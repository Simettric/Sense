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

        $assets_node =  $rootNode->children()->arrayNode('assets')->isRequired();

        /**
         * @var $assets_node_children \Symfony\Component\Config\Definition\Builder\NodeBuilder
         */
        $assets_node_children =  $assets_node->children();



        foreach (array("theme", "admin") as $node_name) {
            $assets_node_children
                ->arrayNode($node_name)
                    ->treatNullLike(array())
                    ->children()
                        ->arrayNode('scripts')
                            ->useAttributeAsKey('handle')

                            ->prototype('array')
                                ->children()
                                    ->scalarNode('file')->defaultNull()->end()
                                    ->enumNode('base_url')
                                        ->values(array("%wp.template_uri%", "%wp.plugin_uri%", null))
                                        ->defaultValue("%wp.template_uri%")
                                    ->end()
                                    ->booleanNode('in_footer')->defaultTrue()->end()
                                    ->scalarNode('version')->defaultValue("1.0")->end()
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
                                    ->values(array("%wp.template_uri%", "%wp.plugin_uri%", null))
                                    ->defaultValue("%wp.template_uri%")
                                ->end()
                                ->scalarNode('version')->defaultValue("1.0")->end()
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
    
    function setAssets(array $config, Sense $sense, $is_admin=false){


        $context = $is_admin ? "admin" : "theme";




        $_assets = $config["assets"][$context];



        foreach($_assets["scripts"] as $handle=>$params){

            $sense["sense.{$context}_assets"]->addScript(
                $handle,
                $params["file"] ? ( $params["base_url"] ? $sense[ $params["base_url"] ] . $params["file"] : $params["file"] ) : null,
                $params["version"],
                $params["in_footer"],
                $params["dependencies"]
            );
        }

        foreach($_assets["styles"] as $handle=>$params){
            /**
             * @var $sense["sense.theme_assets"] AssetManager
             */
            $sense["sense.{$context}_assets"]->addStyle(
                $handle,
                $params["file"] ? ( $params["base_url"] ? $sense[ $params["base_url"] ] . $params["file"] : $params["file"] ) : null,
                $params["version"],
                $params["dependencies"]
            );
        }



    }
}