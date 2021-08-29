# uxmp-core

## Setup

Copy .env.dist to .env and adjust the variables according to your server setup

TBD

## Webserver configuration

The config is based on the assumption of the following foler structure:

- /path/to/root: Document-Root, containing the gui
- /path/to/api: Folder containing the core source

### nginx

```
root /path/to/root;

location / {
        index  index.html;
        try_files $uri $uri/ /index.html;
}

location /api/ {
        alias /path/to/api/src/public/;
        
        try_files $uri $uri/ @nested;
}

location @nested {
        rewrite /api/(.*)$ /api/index.php?/$1 last;
}
```