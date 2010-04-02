# sfAssetsManagerPlugin


symfony 1.3/1.4 plugin to easily maintain javascripts and stylesheets files
and dependencies in one dedicated place.

![symfony assets dependencies manager](http://www.symfony-project.org/uploads/plugins/cb885cd82f4cb190dabe2e54dcedf991.png)


## Purpose

Using this plugin you will be able to maintain your javascripts and stylesheets 
configuration and dependencies easily without changing each of your templates.

You will be able to use relative or absolute path, to include js and css files 
from plugins and to control the location of your assets files.


## Simple example

Create a file /config/assets_manager.yml :

    packages:
      mypage:
        js:   [script1.js, script2.js]
        css:  [style1.css, style2.css]

In your template :

Use the helper and import the "mypage" package :
    <? php use_helper('sfAssetsManager') ?>
    <? php load_assets('mypage') ?>
    

## Advanced example

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
        js:     [jquery.datepicker.js]
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

    <?php use_helper('sfAssetsManager') ?>
    <?php load_assets('homepage') ?>

…/templates/subscribeSuccess.php :

    <?php use_helper('sfAssetsManager') ?>
    <?php load_assets('subscribe') ?>
        

From there, you can easily change your theme, update your jquery version 
, use another datepicker for all your templates by updating the appropriate
package, change framework,… Without modifying each of your templates !



## Configuration


### List of packages :

Packages are based on their names and therfore must be unique.

They must all be under the "packages:" root node.


### Configuring a package :

* import:  string or array. Existing packages to include. Assets 
used in imported packages will be included before the current 
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


## TODO

* Use a config handler to allow multiple assets_manager.yml files 
and to use cache.
* Use a minifier to automatically compact files and make it optional.
