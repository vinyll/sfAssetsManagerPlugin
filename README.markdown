# sfAssetsManagerPlugin


symfony 1.4/1.3/1.2 plugin to easily maintain javascripts and stylesheets files
and dependencies in one dedicated place.

![symfony assets dependencies manager](http://www.symfony-project.org/uploads/plugins/cb885cd82f4cb190dabe2e54dcedf991.png)


## Purpose

Using this plugin you will be able to maintain your javascripts and stylesheets 
configuration and dependencies easily without searching for references in all 
of your templates.

You will be able to use relative or absolute path, to include js and css files 
from plugins and to control the location of your assets files.

You may even use this manager with regular use_stylehsheet() and use_javascript()
helper for specific needs.


## How to install

Download this plugin into your /plugins dir and activate it in 
your ProjectConfiguration.class.php (for example).

    $this->enablePlugins(…, 'sfAssetsManagerPlugin');


## Simple example

Create a file /config/assets_manager.yml :


    packages:
      mypackage:
        js:   [script1.js, script2.js]
        css:  [style1.css, style2.css]

In your template :

Use the helper and import the "mypage" package :

    <? php load_assets('mypackage') ?>
    

## Advanced example

Sample of a config/assets_manager.yml :

    packages:
      jquery:
        js:     http://code.jquery.com/jquery-1.4.2.min.js
      jquery-ui:
        import: jquery
        js:     jquery-ui-1.8.min.js
        css:    themeroller.css
      dragndrop:
        import: jquery-ui
        js:     [jquery.draggable.js, jquery.droppable.js]
      datepicker:
        import: jquery-ui
        js:     jquery.datepicker.js
        css:    yellowDatepicker.css
      layout:
        css:    layout.css
      homepage:
        import: [dragndrop, datepicker, layout]
        js:     [shopCart.js, dateSelect.js]
        css:    dualview.css
      subscribe:
        import: [datepicker, layout]
        css:    niceform.css
        
        
In your templates :

…/templates/homepageSuccess.php :

    <?php load_assets('homepage') ?>

…/templates/subscribeSuccess.php :

    <?php load_assets('subscribe') ?>
        

From there, you can easily change your theme, update your jquery version 
, use another datepicker for all your templates by updating the appropriate
package, change framework,… Without modifying each of your templates !



## Specific examples


### Loading on javascripts or stylesheets

In some cases you should load a package but only its javascripts or 
stylesheets.
That will also only load the specified asset type for imported packages.

Using the above "Advanced example" for loading stylesheets only :

    …    
    <?php load_assets('homepage', 'css') ?>
    …

    
Would import theses stylesheets in current page :

themeroller.css, yellowDatepicker.css, layout.css, dualview.css


### Mixing assets manager with regular calls

    …
    <?php load_assets('package1') ?>
    <?php use_javascript('myfile.js') ?>
    <?php load_assets('package2') ?> 
    …
    

### Calling assets manager from an action or a class

By default it should be called in a template and therefore use the helper.

However, in some case you might need to call a package from an action or a class.

    $manager = new sfAssetsManager();
    $manager->load('mypackage');
    
> See Source code or tests for futher details or methods.


This will use javascript file in the called ordered and will result with 
an inclusion of myfile.js after assets from package1 and before those
from package2. 


### Altering response after its full load

In some case (such a loading a package from a DB rendered content) the sfWebResponse
content is not properly up-to-date with its actual stylesheets and javascripts (symfony bug ?).

In this example case it is useful to alter the js and css tag from the response content.
Therefore, you may activate the alter_response configuration flag and use the appropriate tags.

#### config

    app:
      sf_assets_manager_plugin:
        alter_response: true

### layout

replace 

    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>

with

    <?php echo stylesheet_assets() ?>
    <?php echo javascript_assets() ?>


> Notice that using these helpers would still work the alter_response configuration set to false
> as it would render the regular helpers.


## Configuration


### List of packages :

* Packages are declared in config/assets_manager.yml.
  This file can be in any level of config dir (module, application, 
  project, plugin) and be overriden by each other.
* Packages are based on their names and therfore must be unique.
* They must all be under the "packages:" root node.


### Configuring a package :

* import:  string or array. Existing packages to include. Note that
assets used in imported packages will be included before the current 
package assets.

* js: string or array. javascripts to include.

* css: string or array. stylesheets to include.

> Note that js and css options and treated the same way that the 
Response object usually treats them.
That means if you set a relative path, files will be searched from
/web/js and /web/css.
The assets will be loaded in the same order than the array.


## More

See the tests files for further details.

