# Open Power Cake #

Open Power Cake is a plugin for [CakePHP] web framework, that allows to use [Open Power Template][OPT] system instead of built-in views.

## Installation ##

### Requirements ###

Open Power Cake require obviously Open Power Template library to work. This can be downloaded from its [download page][OPTdownload].

Make sure to check requirements listed [here][OPTreqs].

### Installation process ###

Change the current working directory to `vendors/` directory above Your `app/` directory, and download the source of a plugin using git:

    git clone git://github.com/dejw/opc.git OpenPowerCake

to make the new View class visible in Your application paste the line below at the top of Your `app_controller.php`:

    App::import('Vendor', "OpenPowerCake", array('file' => 'Plugin.php'));

Now You can use bran new Open Power Cake view the same as standard views in CakePHP. E.g. write:

    class AppController extends Controller {
        var $view = "Opc_";
        // ... rest of the code
    }

To enable Open Power Template in whole application.

Template files used by this plugin should end with ".tpl" extension.

## Compatibility ##

Open Power Cake is compatible with CakePHp 1.2.2.8120 and Open Power Template 2.0.4.

## Pending features ##

List below includes features that certainly will be implemented in the future:

1. Configurable plugin parameters
2. Extended `set()` and `get()` methods to handle Object Types from OPT
3. Helpers (for html, ajax etc.)
4. Compatibility test against older CakePHP and OPT versions.

  [CakePHP]: http://cakephp.org/  "CakePHP"
  [OPT]: http://www.invenzzia.org/en/projects/open-power-libraries/open-power-template "Open Power Template"
  [OPTreqs]: http://static.invenzzia.org/docs/opt/2_0/book/en/installation.html "Open Power Template: Requirements"
  [OPTdownload]: http://www.invenzzia.org/en/download/open-power-template/2-0/2-0-4 "Open Power Template: Download page"

