<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">The flexible PHP Framework</p>

<p align="center">
<a href="https://twitter.com/lambirou225"><img src="https://img.shields.io/badge/author-@lambirou225-blue.svg?style=flat-square" alt="Author"></a>
<a href="https://github.com/lambirou/babiphp/blob/master/docs/LICENSE.md"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License"></a>
<a href="https://lambirou.github.io/babiphp"><img src="https://poser.pugx.org/lambirou/babiphp/v/stable.svg?style=flat-square" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/lambirou/babiphp#dev-master"><img src="https://poser.pugx.org/lambirou/babiphp/v/unstable.svg?style=flat-square" alt="Latest Unstable Version"></a>
<!-- <a href="https://packagist.org/packages/lambirou/babiphp"><img src="https://img.shields.io/packagist/dt/lambirou/babiphp.svg?style=flat-square" alt="Total Downloads"></a> -->
</p>

<br>

## Qu'est-ce que BabiPHP?

BabiPHP est un Framework de développement d'applications - un <i>toolkit</i> (trousse à outils) - pour les personnes qui créent des sites Web en PHP. Accessible, mais puissant, fournissant des outils nécessaires aux applications grosses et robustes. Son objectif est de vous permettre de développer des projets beaucoup plus rapidement que lorsque vous écrivez un code à partir de zéro, en fournissant un ensemble de bibliothèques enrichis pour les tâches requises, ainsi qu'une interface simple et une structure logique pour accéder à ces bibliothèques. BabiPHP permet de vous concentrer de manière créative sur votre projet en minimisant la quantité de code nécessaire pour une tâche donnée.

<br>

## Installation

#### Installer via composer:
Première étape:

```
composer require lambirou/babiphp
```
Seconde étape:<br>
Créer un fichier <i>.htaccess</i> à la racine du serveur, ensuite ouvrez le avec un éditeur de texte et ajoutez y les lignes suivantes:

```
<IfModule mod_rewrite.c>

RewriteEngine on
RewriteRule ^$ vendor/lambirou/babiphp/     [L]
RewriteRule (.*) vendor/lambirou/babiphp/$1 [L]

</IfModule>
```
Pour plus de simplicité nous vous recommandons de déplacer le contenu du dossier <i>vendor/lambirou/babiphp/</i> à la racine de votre projet et de supprimer tous les fichiers et dossiers déjà présent (y compris le fichier <i>.htaccess</i> que vous créer précédement).

#### Installation manuelle:

<ol>
<li>Téléchargez l'archive de <a href="https://github.com/lambirou/babiphp/archive/master.zip">BabiPHP</a>.</li>
<li>Décompressez le paquet.</li>
<li>Téléchargez les dossiers et les fichiers de l'archive sur votre projet.</li>
<li>Ouvrez le fichier <i>app/config/Config.php</i> avec un éditeur de texte et définissez votre URL de base.</li>
<li>Si vous avez l'intention d'utiliser un chiffrement ou de gérer des utilisateurs, définissez votre clé de cryptage en éditant le fichier <i>app/config/Security.php</i>.</li>
<li>Si vous avez l'intention d'utiliser une base de données, ouvrez le fichier <i>app/config/Database.php</i> avec un éditeur de texte et définissez les paramètres de votre base de données.</li>
</ol>

Pour des informations plus detaillés veuillez consulter la <a href="https://lambirou.github.io/babiphp" target="blank">documentation</a>.

<br>

<p align="center">
<i>BabiPHP : The Framework Made in Babi (Abidjan) !</i>
</p>
