<p align="center">
    <a href="https://monsieurbiz.com" target="_blank">
        <img src="https://monsieurbiz.com/logo.png" width="250px" alt="Monsieur Biz logo" />
    </a>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <a href="https://monsieurbiz.com/agence-web-experte-sylius" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" width="200px" alt="Sylius logo" />
    </a>
    <br/>
    <img src="https://monsieurbiz.com/assets/images/sylius_badge_extension-artisan.png" width="100" alt="Monsieur Biz is a Sylius Extension Artisan partner">
</p>

<h1 align="center">Media Manager for Sylius</h1>

[![Media Manager Plugin license](https://img.shields.io/github/license/monsieurbiz/SyliusMediaManagerPlugin?public)](https://github.com/monsieurbiz/SyliusMediaManagerPlugin/blob/master/LICENSE.txt)
[![Tests Status](https://img.shields.io/github/workflow/status/monsieurbiz/SyliusMediaManagerPlugin/Tests?logo=github)](https://github.com/monsieurbiz/SyliusMediaManagerPlugin/actions?query=workflow%3ATests)
[![Security Status](https://img.shields.io/github/workflow/status/monsieurbiz/SyliusMediaManagerPlugin/Security?label=security&logo=github)](https://github.com/monsieurbiz/SyliusMediaManagerPlugin/actions?query=workflow%3ASecurity)


![Demo of the media manager](docs/images/demo.gif)

## Installation

<!-- Add this part when recipe will be released

Install the plugin via composer:

```bash
composer require monsieurbiz/sylius-media-manager-plugin
```

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

Copy the plugin configuration files in your `config` folder:

```bash
cp -Rv vendor/monsieurbiz/sylius-media-manager-plugin/recipes/1.0-dev/config/ config
```

Add this config to your `.env` :

```
MONSIEURBIZ_SYLIUS_MEDIA_MANAGER_PUBLIC_FOLDER=%kernel.project_dir%/public
MONSIEURBIZ_SYLIUS_MEDIA_MANAGER_ROOT_FOLDER_FROM_PUBLIC=media
MONSIEURBIZ_SYLIUS_MEDIA_MANAGER_MAX_FILE_SIZE=5M
```

Change your `config/packages/liip_imagine.yaml` to manage the wanted folder :

```
liip_imagine:
    # [...]
    loaders:
        default:
            filesystem:
                data_root:
                    - "%sylius_core.public_dir%/media/image"
                    - "%sylius_core.public_dir%/media"
```

Copy the templates in the folder `dist/templates/` to have the correct macros for form types.

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

## Contributing

You can find a way to run the plugin without effort in the file [DEVELOPMENT.md](./DEVELOPMENT.md).

Then you can open an issue or a Pull Request if you want! 😘  
Thank you!

## License

This plugin is completely free and released under the [MIT License](https://github.com/monsieurbiz/SyliusMediaManagerPlugin/blob/master/LICENSE).
