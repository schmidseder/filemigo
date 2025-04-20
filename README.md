# Filemigo
Filemigo ist ein einfacher **Web File Browser**.

Deine Dateien kannst du damit deinen Freunden und ausgewählten Personen hinter einem sicheren Login zur Verfügung stellen.

Wenn du bereits eine Webseite bei einem Webhoster besitzt, ist die Wahrscheinlichkeit, das Filemigo läuft, sehr hoch, da es in PHP programmiert ist.

## Installation
### Voraussetzungen
- PHP8.3
- Filemigo funktioniert zur Sicherheit nur über HTTPS

### Manuelle Installation auf Webspace
Download Filemigo:  
[`https://github.com/schmidseder/filemigo/archive/refs/heads/main.zip`](`https://github.com/schmidseder/filemigo/archive/refs/heads/main.zip`)

Download POOL:  
[`https://github.com/schmidseder/pool/archive/refs/heads/develop.zip`](`https://github.com/schmidseder/pool/archive/refs/heads/develop.zip`)

Beide ZIP-Archive entpacken und Verzeichnisse umbenennnen:  
`mv filemigo-main filemigo`  
`mv pool-develop pool`

In das Hauptverzeichnis des Webspace kopieren (hochladen).  
Zusätzlich zwei Verzeichnisse `data` und `tmp` parallel anlegen.  
In das `data` Verzeichnis werden die Dateien gespeichert, auf die man über Filemigo Zugriff hat.  
Das `tmp` Verzeichnis wird zum kurzzeitigen Speichern der generierten ZIP-Archive verwendet.

```
/                       
├── public            # Öffentliches Hauptverzeichnis  
│   ├── filemigo
│   └── pool
├── data              # Datenverzeichnis (nicht öffentlich zugänglich)                 
└── tmp               # Temp-Verzeichnis (nicht öffenttlich zugänglich)  
```

## Technologien
- Frontend: PicoCSS, Vanilla Javascript
- Backend: PHP POOL Framework

## Lizenz
[GPLv3](LICENSE)