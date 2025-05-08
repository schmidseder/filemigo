# Filemigo
Filemigo is a simple **web-based file browser**.

It allows you to securely share your files (read-only) with friends or selected users through a protected login.

If you already have a website hosted by a typical web hosting provider, chances are high that
Filemigo will work out of the box - it's built with PHP and designed for easy deployment.

## Installation
### Requirements
- PHP 8.3
- For security reasons, Filemigo only works via HTTPS

### Manual Installation on Webspace

![Installation Demo](https://demo.schmidseder.net/filemigo-demo-animation.apng)

Download Filemigo:  
[`https://github.com/schmidseder/filemigo/archive/refs/heads/main.zip`](https://github.com/schmidseder/filemigo/archive/refs/heads/main.zip)

Download POOL:  
[`https://github.com/schmidseder/pool/archive/refs/heads/develop.zip`](https://github.com/schmidseder/pool/archive/refs/heads/develop.zip)

Unzip both archives and rename the directories.

Rename the directory `filemigo-main` to `filemigo`.  
Rename the directory `pool-develop` to `pool`.

A file named `example-filemigo.inc.php` is located in the config subdirectory of the `filemigo` directory (`filemigo/config`).
To enable the configuration, rename the file to `filemigo.inc.php`.


Upload both directories with all files to the public document root directory of your webspace.  
Here in the example it is the directory named `public`

Next, create two more directories (`data` and `tmp`) on the same level as `public` so that these directories are not publicly accessible.  
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

Use the user `filemigo` with the password `filemigo` for the first login.  
After you have logged in, generate password hashes for new users, which you copy into the configuration file `filemigo.inc.php`.

You must then remove the user filemigo from the file for security reasons.

## Technologies
- Frontend: PicoCSS, Vanilla JavaScript
- Backend: PHP POOL Framework

## License
[GPLv3](LICENSE)