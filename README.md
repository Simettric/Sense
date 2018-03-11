Sense Framework
=====

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/b19462fc-cfac-46b1-bab6-2dee114ef097/big.png)](https://insight.sensiolabs.com/projects/b19462fc-cfac-46b1-bab6-2dee114ef097)

[![Build Status](https://travis-ci.org/Simettric/Sense.svg?branch=master)](https://travis-ci.org/Simettric/Sense)

Sense is a MVC Framework designed to build complex websites and web applications based on WordPress.

### Code your WordPress plugins and themes using best practices
 

**YOUR CONTROLLER**

```php
class TestController extends AbstractController{
        
   /**
     * @Route("/profile/{name_slug}", name="profile_detail")
     */
    function demoAction($name_slug, \WP_Query $wp_query, \Request $request) {
             
        $repository = $this->get("repository.user");
        $user = $repository->findBy("username", $name_slug);
             
        return $this->resultTemplate('User/profile.php', array(
            "user" => $user
        ));
    }
}
```
    
**YOUR VIEW**

```php
<?php
//your-plugin-path/View/User/profile.php     

    get_header();
        
    $user = sense_view()->get("user");
?>
     
<h1><?php echo $user->getName() ?></h1>
     
<?php
    
    get_footer();
```

(Yes, you can use Twig if you want too ;)
    
### [Read the documentation](http://sense.readthedocs.io/en/latest/) 

