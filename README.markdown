# Open Power Cake #

Open Power Cake is a plugin for [CakePHP] web framework, that allows to use [Open Power Template][OPT] system instead of built-in views.

## Requirements ##

1. CakePHP ([download page][CakePHPdownload])
2. Open Power Template library ([download page][OPTdownload])

Make sure to check Open Power Template requirements listed [here][OPTreqs].

## Installation ##

Change the current working directory to `vendors/` directory above Your `app/` directory, and download the source of a plugin using git:

    git clone git://github.com/dejw/opc.git OpenPowerCake

After this operations You should have directory structure similar to this:

    app/
       ...
    cake/
       ...
    vendors/
       js/
       css/
       shells/
       OpenPowerCake/
          libs/
          src/
          ...

Next copy whole Open Power Template `libs` directory into `vendors/OpenPowerCake/libs/`.

### Configuration ###

To make the new View class visible in Your application paste the line below at the top of Your `app_controller.php`:

    App::import('Vendor', "OpenPowerCake", array('file' => 'Plugin.php'));

Now You can use brand new Open Power Cake view the same as standard views in CakePHP. E.g. write:

    class AppController extends Controller {
        var $view = "Opc_";
        // ... rest of the code
    }

To enable Open Power Template in whole application. `Opc_` class behaves exactly the same as standard view classes so it can be turned on per-controller or per-action by writing:

    class MyController extends AppController {
        /* OPT will render templates for MyController */
        var $view = "Opc_";

        /* OPT will render templates only for someAction in MyController */
        function someAction(){
            $this->view = "Opc_";
        }
    }


Template files used by this plugin should end with `.tpl` extension.

## Basic usage ##

Open Power Cake uses template inheritance to render views. See [this section][OPTinheritance] for details.

### Helpers ###

Thanks to OPT's extensibility Open Power Cake introduces some extra instructions, which adds some flavour to Your Cake.

#### opt:link_to ####

Generates anchor from given controller and action:

    <opt:link_to controller="users" action="login">Anchor</opt:link_to>

It takes optional `full` attribute that makes generated URLs to be absolute.

If You need to pass some unnamed parameters to generated url You can write arbitrary number of attributes started with `param*`. This attributes will be sorted in alphabetical order and passed to `Router::url()` method, thus:

    <opt:link_to controller="articles" action="show" parama="1" paramA="2">Anchor</opt:link_to>

Produces:

    <a href="/articles/show/2/1">Anchor</a>

since `A < a` when ASCII codes are taken into consideration.

This tag also takes `anchor` optional argument which allows You to write simple tags:

    <opt:link_to anchor="Anchor" controller="users" action="login" />


## Compatibility ##

Open Power Cake is compatible with CakePHp 1.2.2.8120 and Open Power Template 2.0.4.

## Future ##

List below includes features that certainly will be implemented in the future:

1. Configurable plugin parameters
2. Extended `set()` and `get()` methods to handle Object Types from OPT
3. Helpers (for html, ajax etc.)
4. Compatibility test against older CakePHP and OPT versions.
5. Alter not only view class, but also whole rendering system

  [CakePHP]: http://cakephp.org/  "CakePHP"
  [CakePHPdownload]: http://github.com/cakephp/cakephp1x/downloads "CakePHP: downloads"
  [OPT]: http://www.invenzzia.org/en/projects/open-power-libraries/open-power-template "Open Power Template"
  [OPTreqs]: http://static.invenzzia.org/docs/opt/2_0/book/en/installation.html "Open Power Template: Requirements"
  [OPTdownload]: http://www.invenzzia.org/en/download/open-power-template/2-0/2-0-4 "Open Power Template: Download page"
  [OPTinheritance]: http://static.invenzzia.org/docs/opt/2_0/book/en/syntax.topics.modularization.inheritance.html "Open Power Template: Template inheritance"

