<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">The flexible PHP Framework</p>

<p align="center">
<a href="https://twitter.com/lambirou225"><img src="https://img.shields.io/badge/author-@lambirou225-blue.svg?style=flat-square" alt="Author"></a>
<a href="https://github.com/lambirou/babiphp/blob/master/docs/license.rst"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License"></a>
<a href="https://packagist.org/packages/lambirou/babiphp"><img src="https://img.shields.io/packagist/v/lambirou/babiphp.svg?style=flat-square" alt="Packagist Version"></a>
<a href="https://lambirou.github.io/babiphp"><img src="https://poser.pugx.org/lambirou/babiphp/v/stable.svg?style=flat-square" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/lambirou/babiphp"><img src="https://img.shields.io/packagist/dt/lambirou/babiphp.svg?style=flat-square" alt="Total Downloads"></a>
</p>

<br>

## Qu'est-ce que BabiPHP?

BabiPHP est un Framework de développement d'applications - un <i>toolkit</i> (trousse à outils) - pour les personnes qui créent des sites Web en PHP. Accessible, mais puissant, fournissant des outils nécessaires aux applications grosses et robustes. Son objectif est de vous permettre de développer des projets beaucoup plus rapidement que lorsque vous écrivez un code à partir de zéro, en fournissant un ensemble de bibliothèques enrichis pour les tâches requises, ainsi qu'une interface simple et une structure logique pour accéder à ces bibliothèques. BabiPHP permet de vous concentrer de manière créative sur votre projet en minimisant la quantité de code nécessaire pour une tâche donnée.

<br>

## Exigences du serveur

La version 5.6.4 ou plus récente de PHP est recommandée.

<br>

## Installation

#### Installer via composer:
Première étape:

```
composer require lambirou/babiphp
```
Seconde étape:

Créer un fichier <i>.htaccess</i> à la racine du serveur, ensuite ouvrez le avec un éditeur de texte et ajoutez y les lignes suivantes:

```
<IfModule mod_rewrite.c>

RewriteEngine on
RewriteRule ^$ vendor/lambirou/babiphp/     [L]
RewriteRule (.*) vendor/lambirou/babiphp/$1 [L]

</IfModule>
```

#### Installation manuelle:

<ol>
<li>Téléchargez l'archive de <a href="https://github.com/lambirou/babiphp/archive/master.zip">BabiPHP</a>.</li>
<li>Décompressez le paquet.</li>
<li>Téléchargez les dossiers et les fichiers de l'archive sur votre serveur.</li>
<li>Ouvrez le fichier <i>app/config/Config.php</i> avec un éditeur de texte et définissez votre URL de base.</li>
<li>Si vous avez l'intention d'utiliser un chiffrement ou de gérer des utilisateurs, définissez votre clé de cryptage en éditant le fichier <i>app/config/Security.php</i>.</li>
<li>Si vous avez l'intention d'utiliser une base de données, ouvrez le fichier <i>app/config/Database.php</i> avec un éditeur de texte et définissez les paramètres de votre base de données.</li>
</ol>

Pour une meilleure sécurité, le système et tous les dossiers de l'application doivent être placés au-dessus de la racine Web afin qu'ils ne soient pas directement accessibles via un navigateur. Par défaut, les fichiers <b>.htaccess</b> sont inclus dans chaque dossier pour empêcher l'accès direct, mais il est préférable de les supprimer de l'accès public entièrement au cas où la configuration du serveur Web changerait ou ne respecterait pas le <b>.htaccess</b>.

Une mesure supplémentaire à prendre dans les environnements de production est de désactiver les rapports d'erreur PHP et toute autre fonctionnalité de développement. Dans BabiPHP, cela peut se faire en suivant les configurations décrites sur la page de sécurité .

C'est tout!

Si vous êtes nouveau sur BabiPHP, lisez la section Mise en route du Guide de l'utilisateur (situé dans le repertoire <b>docs/</b>) pour commencer à apprendre comment créer des applications PHP dynamiques.

<br>

## Mise en route de BabiPHP

Toute application logicielle nécessite des efforts d'apprentissage. Nous avons fait de notre mieux pour minimiser le processus en le rendant aussi agréable que possible.

BabiPHP est framework <i>ready-to-use</i> (prêt à l'emploi), l'étape majeure consiste donc à l'installer, alors lisez le sujet sur l'installation dans la section ci-dessus.

Ensuite, lisez chaque page des sujets généraux dans l'ordre. Chaque sujet s'appuie sur le précédent, et comprend des exemples de code que vous êtes encouragés à essayer.

Une fois que vous comprenez les bases, vous serez en mesure d'explorer les pages de référence de classe et de référence d'aide pour apprendre à utiliser les différentes bibliothèques.

<br>

## Journal des modifications et Nouvelles fonctionnalités

Vous pouvez trouver une liste de toutes les modifications pour chaque version dans le <a href="https://github.com/lambirou/babiphp/blob/master/docs/changelog.rst">journal des modifications</a> du guide de l'utilisateur.

<br>

## Vulnérabilités de sécurité

Si vous découvrez une vulnérabilité de sécurité au sein de BabiPHP, veuillez envoyer un courrier électronique à Roland Edi à contact.lambirou@gmail.com. Toutes les vulnérabilités de sécurité seront traitées rapidement.

<br>

## License

Consultez le [Contrat de Licence](https://github.com/lambirou/babiphp/blob/master/docs/license.rst).

<br>

## Reconnaissance

L'équipe BabiPHP tient à remercier tous les contributeurs au projet et vous, l'utilisateur de BabiPHP.

<br>

<p align="center">
<i>BabiPHP : The Framework Made in Babi (Abidjan) !</i>
</p>
