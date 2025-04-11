# UPGRADE FROM `1.2` TO `2.0`

**Replace form type option name**

If you use one of the following options with Media Manager form type (`AudioType`, `FaviconType`, `ImageType`, `PdfType` or `VideoType`), 
you had to rename it as follows:

* `["file-type" => "TYPE"]` became `["file_type" => "TYPE"]`
* `["filter-width" => INT]` became `["filter_width" => INT]]`

**Use new services**

`FileHelper` service has been split into dedicated services:
* `FileRepositoryInterface` to list file from a folder or get one file by it path
* `DirectoryOperatorInterface` where all directory operations takes place
* `FileMetadataResolverInterface` for file types resolution
* `FilePathResolverInterface` for absolute and relative path resolution
* `FileValidatorInterface` for file validation

If you use `FileHelper` service, you need to inject one of the service above instead.
