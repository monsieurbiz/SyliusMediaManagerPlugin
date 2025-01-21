[![Banner of Sylius Media Manager plugin](docs/images/banner.jpg)](https://monsieurbiz.com/agence-web-experte-sylius)

<h1 align="center">Sylius Media Manager</h1>

[![Media Manager Plugin license](https://img.shields.io/github/license/monsieurbiz/SyliusMediaManagerPlugin?public)](https://github.com/monsieurbiz/SyliusMediaManagerPlugin/blob/master/LICENSE.txt)
[![Tests Status](https://img.shields.io/github/actions/workflow/status/monsieurbiz/SyliusMediaManagerPlugin/tests.yaml?branch=master&logo=github)](https://github.com/monsieurbiz/SyliusMediaManagerPlugin/actions?query=workflow%3ATests)
[![Recipe Status](https://img.shields.io/github/actions/workflow/status/monsieurbiz/SyliusMediaManagerPlugin/recipe.yaml?branch=master&label=recipes&logo=github)](https://github.com/monsieurbiz/SyliusMediaManagerPlugin/actions?query=workflow%3ASecurity)
[![Security Status](https://img.shields.io/github/actions/workflow/status/monsieurbiz/SyliusMediaManagerPlugin/security.yaml?branch=master&label=security&logo=github)](https://github.com/monsieurbiz/SyliusMediaManagerPlugin/actions?query=workflow%3ASecurity)

This plugin adds a media manager to your images, videos and other files type fields in Sylius.

![Demo of the media manager](docs/images/demo.gif)

## Compatibility

| Sylius Version | PHP Version     |
|----------------|-----------------|
| 1.12           | 8.1 - 8.2 - 8.3 |
| 1.13           | 8.1 - 8.2 - 8.3 |
| 1.14           | 8.1 - 8.2 - 8.3 |

## Installation

If you want to use our recipes, you can configure your composer.json by running:

```bash
composer config --no-plugins --json extra.symfony.endpoint '["https://api.github.com/repos/monsieurbiz/symfony-recipes/contents/index.json?ref=flex/master","flex://defaults"]'
```

Install the plugin via composer:

```bash
composer require monsieurbiz/sylius-media-manager-plugin
```

<!-- The section on the flex recipe will be displayed when the flex recipe will be available on contrib repo
<details><summary>For the installation without flex, follow these additional steps</summary>
-->

Change your `config/bundles.php` file to add this line for the plugin declaration:

```php
<?php

return [
    //..
    MonsieurBiz\SyliusMediaManagerPlugin\MonsieurBizSyliusMediaManagerPlugin::class => ['all' => true],
];
```

Copy the plugin configuration files in your `config` folder: https://github.com/monsieurbiz/symfony-recipes/tree/master/monsieurbiz/sylius-media-manager-plugin/1.0/config

Add these variables to your `.env` :

```
MONSIEURBIZ_SYLIUS_MEDIA_MANAGER_PUBLIC_FOLDER=%kernel.project_dir%/public
MONSIEURBIZ_SYLIUS_MEDIA_MANAGER_ROOT_FOLDER_FROM_PUBLIC=media
MONSIEURBIZ_SYLIUS_MEDIA_MANAGER_MAX_FILE_SIZE=5M
```
<!-- </details> -->

Copy the templates in the folder `dist/templates/` to ensure that form fields are rendered correctly:

```
cp -R vendor/monsieurbiz/sylius-media-manager-plugin/dist/templates/bundles/* templates/bundles/
```

Copy the form extension if you want to use it on your product images.
```
cp -R vendor/monsieurbiz/sylius-media-manager-plugin/dist/src/Form/Extension/ProductImageTypeExtension.php src/Form/Extension/ProductImageTypeExtension.php
```

Else remove the file `templates/bundles/SyliusAdminBundle/Form/imagesTheme.html.twig`

## Use form types

You can check the [dist](https://github.com/monsieurbiz/SyliusMediaManagerPlugin/tree/master/dist) folder
to check how the plugin is setup on the test application.

### Images

Use `MonsieurBiz\SyliusMediaManagerPlugin\Form\Type\ImageType`

### PDF

Use `MonsieurBiz\SyliusMediaManagerPlugin\Form\Type\PdfType`

### Video

Use `MonsieurBiz\SyliusMediaManagerPlugin\Form\Type\VideoType`

### Favicon

Use `MonsieurBiz\SyliusMediaManagerPlugin\Form\Type\FaviconType`

### Audio

Use `MonsieurBiz\SyliusMediaManagerPlugin\Form\Type\AudioType`

## Contributing

You can find a way to run the plugin without effort in the file [DEVELOPMENT.md](./DEVELOPMENT.md).

Then you can open an issue or a Pull Request if you want! 😘  
Thank you!

## License

This plugin is completely free and released under the [MIT License](https://github.com/monsieurbiz/SyliusMediaManagerPlugin/blob/master/LICENSE).
