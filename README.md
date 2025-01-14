# Signature de PDF

Logiciel web libre permettant de signer un PDF.

## Instances

Liste des instances permettant d'utiliser ce logiciel :

- [pdf.24eme.fr](https://pdf.24eme.fr)
- [pdf.libreon.fr](https://pdf.libreon.fr)

## License

Logiciel libre sous license AGPL V3

## Installation

Dépendances :

- php >= 5.6 
- rsvg-convert
- pdftk
- imagemagick
- potrace

Sur debian :

```
sudo aptitude install php librsvg2-bin pdftk imagemagick potrace
```

Récupération des sources :

```
git clone https://github.com/24eme/signaturepdf.git
```

Pour le lancer :

```
php -S localhost:8000 -t public
```

### Configuration de PHP

```
upload_max_filesize = 24M # Taille maximum du fichier PDF à signer
post_max_size = 24M # Taille maximum du fichier PDF à signer
max_file_uploads = 201 # Nombre de pages maximum du PDF, ici 200 pages + le PDF d'origine
```

### Déployer avec apache

```
DocumentRoot /path/to/signaturepdf/public

<Directory /path/to/signaturepdf/public>
    Require all granted
    FallbackResource /index.php
    php_value max_file_uploads 201
    php_value upload_max_filesize 24M
    php_value post_max_size 24M
</Directory>
```

### Mise à jour vers la dernière version

La dernière version stable est sur la branche `master`, pour la mise à jour il suffit de récupérer les dernières modifications :

```
git pull -r
```

### Activation et configuration du mode partage de signature à plusieurs

Ce mode permet de proposer la signature d'un pdf à plusieurs personnes mais il nécessite que les PDF soient stockés sur le serveur, il convient donc de définir un dossier qui contiendra ces PDF.

Il n'est pas obligatoire d'activer ce mode pour que l'application fonctionne c'est une option.

Créer le fichier `config/config.ini`

```
cp config/config.ini{.example,}
```

Dans ce fichier `config/config.ini`, il suffit ce configurer la variable `PDF_STORAGE_PATH` avec le chemin vers lequel les fichiers pdf uploadés pourront être stockés :
```
PDF_STORAGE_PATH=/path/to/folder
```

Créer ce dossier :
```
mkdir /path/to/folder
```

Le serveur web devra avoir les droits en écriture sur ce dossier.

Par exemple pour apache :
```
chown www-data /path/to/folder/to/store/pdf
```

### Déployer avec docker

#### Construction de l'image

```bash
docker build -t signaturepdf .
````

#### Lancement d'un conteneur

```bash
docker run -d --name=signaturepdf -p 8080:80 signaturepdf
````

[localhost:8080](http://localhost:8080)

#### Configuration

Les variables suivantes permettent de configurer le déployement :

|Variable|description|exemple|defaut|
|-----|-----|-----|-----|
|`SERVERNAME`|url de déploiement|`pdf.24eme.fr`|localhost|
|`UPLOAD_MAX_FILESIZE`|Taille maximum du fichier PDF à signer|48M|24M|
|`POST_MAX_SIZE`|Taille maximum du fichier PDF à signer|48M|24M|
|`MAX_FILE_UPLOADS`|Nombre de pages maximum du PDF, ici 200 pages + le PDF d'origine|401|201|
|`PDF_STORAGE_PATH`|chemin vers lequel les fichiers pdf uploadés pourront être stockés|/data||

```bash
docker run -d --name=signaturepdf -p 8080:80 -e SERVERNAME=pdf.example.org -e UPLOAD_MAX_FILESIZE=48M -e POST_MAX_SIZE=48M -e MAX_FILE_UPLOADS=401 -e PDF_STORAGE_PATH=/data signaturepdf
````

## Tests

Pour exécuter les tests fonctionnels :

```
make test
```

Les tests sont réalisés avec `puppeteer` et `jest`.

Pour lancer les tests et voir le navigateur (en mode debug) :

```
DEBUG=1 make test
```

## Librairies utilisées

- **Fat-Free** micro framework PHP : https://github.com/bcosca/fatfree (GPLv3)
- **Bootstrap** framework html, css et javascript : https://getbootstrap.com/ (MIT)
- **PDF.js** librairie de lecture de PDF dans un canvas HTML : https://github.com/mozilla/pdf.js (Apache-2.0)
- **Fabric.js** librairie pour manipuler un canvas HTML : https://github.com/fabricjs/fabric.js (MIT)
- **PDFtk** outils de manipulation de PDF (GPL)
- **librsvg** outils de manipulation de SVG : https://gitlab.gnome.org/GNOME/librsvg (LGPL-2+)
- **potrace** outils de transformation d'image bitamp en image vectorisé : http://potrace.sourceforge.net/ (GPLv2)
- **OpenType.js** outils de transformation d'un texte et sa police en chemin : https://github.com/opentypejs/opentype.js (MIT)
- **ImageMagick** ensemble d'outils de manipulation d'images : https://imagemagick.org/ (Apache-2.0)
- **Caveat** police de caractères style écriture à la main : https://github.com/googlefonts/caveat (OFL-1.1)

Pour les tests :

- **Jest** Framework de Test Javascript : https://jestjs.io/ (MIT)
- **Puppeteer** librairie Node.js pour contrôler un navigateur : https://github.com/puppeteer/puppeteer (Apache-2.0)

## Contributeurs

- Vincent LAURENT (24ème)
- Le Metayer Jean-Baptiste (24ème)
- Xavier Garnier (Logilab)
- Simon Chabot (Logilab)
- Gabriel POMA (24ème)

Logilab a apporté une contribution financière de 1 365 € TTC à la société 24ème pour développer le mode multi signature.

Le développement du logiciel a principalement été réalisé sur le temps de travail de salariés du 24ème.



