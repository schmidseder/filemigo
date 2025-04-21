# Filemigo
Filemigo is a simple **web-based file browser**.

It lets you securely share files with friends or selected users through a protected login.

If you already have a website hosted by a typical web hosting provider, chances are high that  
Filemigo will work out of the box — it's built with PHP and designed for easy deployment.


## Installation
### Requirements
- PHP 8.3
- For security reasons, Filemigo only works via HTTPS

### Manual Installation on Webspace
Download Filemigo:  
[`https://github.com/schmidseder/filemigo/archive/refs/heads/main.zip`](https://github.com/schmidseder/filemigo/archive/refs/heads/main.zip)

Download POOL:  
[`https://github.com/schmidseder/pool/archive/refs/heads/develop.zip`](https://github.com/schmidseder/pool/archive/refs/heads/develop.zip)

Unzip both archives and rename the directories.

Rename the directory `filemigo-main` to `filemigo`.  
Rename the directory `pool-develop` to `pool` .  

Upload both directories with all files to the public document root directory of your webspace.  
Here in the example it is the directory named `public`

Next, create two additional directories at the same level: `data` and `tmp`.  
The `data` directory stores the files that are accessible through Filemigo.  
The `tmp` directory is used to temporarily hold generated ZIP archives.

```
/                       
├── public            # Public root directory 
│   ├── filemigo
│   └── pool
├── data              # Data directory (not publicly accessible)                 
└── tmp               # Temp directory (not publicly accessible)  
```
A file named `example-filemigo.inc.php` is located in the config subdirectory of the Filemigo application (`filemigo/config`).
To enable the configuration, rename the file to `filemigo.inc.php`.


## Technologies
- Frontend: PicoCSS, Vanilla JavaScript
- Backend: PHP POOL Framework

## License
[GPLv3](LICENSE)