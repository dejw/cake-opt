# Open Power Cake #

**Open Power Cake** is a plugin for [CakePHP] web framework, that allows You to use [Open Power Template][OPT] system instead of built-in views.

**Open Power Template** is a web template engine that uses a dedicated XML template language for writing templates. It is not a general-purpose, but a domain-specific language. It was primarily designed to support and simplify template-specific problems with a set of declarative instructions. See [Wikipedia][OPTwiki] for details.

## Requirements ##

1. CakePHP ([download page][CakePHPdownload])
2. Open Power Template library ([download page][OPTdownload])

Make sure to check Open Power Template requirements listed [here][OPTreqs].

## Installation ##

Change the current working directory to `vendors/` directory above Your `app/` directory, and download the source of a plugin using git:

    git clone git://github.com/dejw/cake-opt.git OpenPowerCake

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
        var $view = "OpenPowerTemplate";
        // ... rest of the code
    }

To enable Open Power Template in whole application. `OpenPowerTemplate` class behaves exactly the same as standard view classes so it can be turned on per-controller or per-action by writing:

    class MyController extends AppController {
        /* OPT will render templates for MyController */
        var $view = "OpenPowerTemplate";

        /* OPT will render templates only for someAction in MyController */
        function someAction(){
            $this->view = "OpenPowerTemplate";
        }
    }


Template files used by this plugin should end with `.tpl` extension.

### config/core.php ###

Open Power Cake uses built-in Cake's configuration module to set-up template system. Following variables can be used in Your `config/core.php` file:

 * **Opt.compileDir**

   Points at directory, relative to `APP/views`, where compiled templates will be stored. It should be writeable by Your web-server in order to make Open Power Template works. Default value is `/views/compiled`.

## Basic usage ##

Open Power Cake uses template inheritance to render views. See [this section][OPTinheritance] for details.

### Helpers ###

Thanks to OPT's extensibility Open Power Cake introduces some extra instructions, which adds some flavour to Your Cake.

#### cake:link_to ####

Generates anchor from given controller and action:

    <cake:link_to controller="users" action="login">Anchor</cake:link_to>

It takes optional `full` attribute that makes generated URLs to be absolute.

If You need to pass some unnamed parameters to generated url You can write arbitrary number of attributes started with `param*`. This attributes will be sorted in alphabetical order and passed to `Router::url()` method, thus:

    <cake:link_to controller="articles" action="show" parama="1" paramA="2">Anchor</cake:link_to>

Produces:

    <a href="/articles/show/2/1">Anchor</a>

since `A < a` when ASCII codes are taken into consideration.

This tag also takes `anchor` optional argument which allows You to write simple tags:

    <cake:link_to anchor="Anchor" controller="users" action="login" />

#### cake:url ####

Generates URL (the same as `cake:link_to` tag) and adds it as an attribute to the parent tag:

    <a id="my-id">
        <cake:url controller="users" action="login" />
        Anchor
    </a>

might produce:

    <a id="my-id" href="/users/login">
        Anchor
    </a>

Url is inserted as value of `href` tag by default, but You can change this behaviour by specifying the `attribute`:

    <form method="post">
        <cake:url controller="users" action="login" attribute="action" />
        <!-- rest of a form -->
    </form>

## Compatibility ##

Open Power Cake is compatible with CakePHp 1.2.2.8120 and Open Power Template 2.0.4.

## Future ##

List below includes features that certainly will be implemented in the future:

1. Extended `set()` and `get()` methods to handle Object Types from OPT
2. Helpers (for html, ajax etc.)
3. Compatibility test against older CakePHP and OPT versions.
4. Alter not only view class, but also whole rendering system

  [CakePHP]: http://cakephp.org/  "CakePHP"
  [CakePHPdownload]: http://github.com/cakephp/cakephp1x/downloads "CakePHP: downloads"
  [OPTwiki]: http://en.wikipedia.org/wiki/Open_Power_Template ""Open Power Template: Wikipedia"
  [OPT]: http://www.invenzzia.org/en/projects/open-power-libraries/open-power-template "Open Power Template"
  [OPTreqs]: http://static.invenzzia.org/docs/opt/2_0/book/en/installation.html "Open Power Template: Requirements"
  [OPTdownload]: http://www.invenzzia.org/en/download/open-power-template/2-0/2-0-4 "Open Power Template: Download page"
  [OPTinheritance]: http://static.invenzzia.org/docs/opt/2_0/book/en/syntax.topics.modularization.inheritance.html "Open Power Template: Template inheritance"

