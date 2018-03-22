# Installation
Autre gestion du Portier Doorbird
  
Ce plugin est principalement dédié à l'utilisation du Doorbird D2101V avec firmware >= 000110 (nouvelle API).  
    
### Les fonctionnalités

![STEP0](https://i.imgur.com/uq355za.png)  
  
*Ce plugin installe:*
  
* Un état STATUT qui donne l'état de la connexion au Doorbird, et fournira le type d'appareil, la référence du firmware le cas échéant, le nombre de relais utilisables.  
* Un actionneur COMMANDES qui vous permet, depuis eedomus, de réaliser des actions sur le portier : Allumer la lumière, enregistrer un snapshot sur le FTP, ouvrir un relais
* Un état EVENTS qui est positionné par le Doorbird lui-même en fonction d'évènements (Sonnette, Mouvement, Relais, RFID) réutilisables dans vos règles.  
* Un état ANALYTICS qui fournit les statistiques d'utilisation  
  
  
L'état EVENTS permet également d'initialiser le Doorbird avec les différentes requêtes d'appels à l'API eedomus (INITIALIZE).    
  
![STEP1](https://i.imgur.com/pp1EPX4.png)  
    
### Prérequis pour l'image
  
Avant d'installer le plugin, vous pouvez créer un nouveau périphérique eedomus de type "Caméra - Autre", afin d'y déposer les captures du Doorbird.  
Depuis "Configuration > Ajouter ou supprimer un périphérique > Ajouter un autre type de caméra > Caméra - Autre  
et saisissez les données selon l'illustration suivante.  
  
![STEP8](https://i.imgur.com/Jk3isEs.png)   
  
Conserver le login/mdp FTP pour la suite de l'installation.  
NB : Vous ne pourrez pas accéder au direct.  
  
    
### Installation du plugin
    
*Voici les différents champs à renseigner:*

* [Obligatoire] - Le nom du Portier Doorbird
* [Obligatoire] - L'IP locale du Doorbird
* [Obligatoire] - L'identifiant Doorbird (Utilisateur habilité de type API-Operator dans l'application Doorbird)
* [Obligatoire] - Le mot de passe associé
* [Optionnel]   - L'adresse du serveur FTP (par défaut : camera.eedomus.com)
* [Optionnel]   - Le login pour l'accès au serveur FTP
* [Optionnel]   - Le mot de passe pour l'accès au serveur FTP
* [Obligatoire] - Votre code API User eedomus
* [Obligatoire] - Votre code API Secret eedomus
  
![STEP3](https://i.imgur.com/TJHRlt9.png)  
  
  
### Paramétrage RFID
  
Si vous utilisez des badges RFID,  
après les avoir enregistrés dans l'application smarpthone Doorbird, récupérez les différents codes de transpondeur afin de paramétrer le capteur "EVENTS" eedomus.  
  
Entrez dans la configuration du capteur "EVENTS", et affichez les valeurs masquées.  
Pour les différents RFID utilisés, saisissez le code du transpondeur entre parenthèses, à la place de la mention "transponder_id".  
Après les parenthèses, vous pouvez écrire le nom du titulaire du badge par exemple, pour vous aider dans vos futures règles.  
Puis enregistrez les modifications.  
   
![STEP2](https://i.imgur.com/8zT6UNl.png) 
  
  
### Première Utilisation
  
Si les données de connexion sont correctes, le capteur STATUT vous confirmera le type d'appareil, le firmware associé et le nombre de relais autorisés (le polling du statut est à 30 mn).  
Le nombre de relais autorisé dépend du paramétrage du portier dans l'application Doorbird : vous pouvez désactiver l'utilisation de certains relais.  
En cas de problème de connexion, le capteur vous indiquera "Doorbird Connection Error".  
  
Pour vous en assurer, lancez l'action "SnapshotFTP" du périphérique "COMMANDES" pour afficher une première capture dans l'image Caméra Doorbird FTP.
  
Si tout est OK, et une fois le paramétrage RFID réalisé, lancez l'action "INITIALIZE" sur l'actionneur "EVENTS" depuis l'interface eedomus :  
  
![STEP4](https://i.imgur.com/fQkFFx0.png) 
  
Cette action créera l'ensemble des "Favorites" pour le Doorbird (="Visite HTTP" dans l'application smartphone Doorbird en français).  
Il s'agit des différentes requêtes HTTP vers l'API eedomus permettant de positionner le capteur "EVENTS" sur l'évènement associé du Doorbird.  
    
Vous retrouverez donc ensuite ces différentes requêtes http dans le menu "Favoris -> Visite HTTP" du menu Administration de l'application :  
  
![STEP5](https://i.imgur.com/ywLtqq2.jpg) 
  
  
En même temps, cela créera le paramétrage de base suivant pour les évènements dans votre application Doorbird (Schedules) :  
  
* Pour la sonnette : Notification smartphone + Appel HTTP eedomus #1 (Doorbell)  
* Pour un mouvement : Appel HTTP eedomus #2 (Motion)  
* Pour chacun des relais autorisé : Appel HTTP eedomus #1x (Relay)  
* Pour un RFID : Activation du relais n°1 + Appel HTTP eedomus #2x (RFID)  
  
Voici par exemple ce que vous retrouverez dans l'application pour le "Calendrier Sonnette" du menu d'Administration :
  
* Une notification push activée quels que soient le jour de la semaine et l'heure  
* Un appel HTTP au favoris "Doorbell" quels que soient le jour de la semaine et l'heure  
  
![STEP6](https://i.imgur.com/TBm8k8A.png) 
![STEP7](https://i.imgur.com/UYtgNuX.png)  
  
Libre à vous ensuite, depuis l'application Doorbird, de modifier les paramètres de jour et d'horaires pour activer ou non le fonctionnement des notifications et de l'appel HTTP liés à la sonnette...  
  
NB : Si vous modifiez les calendriers (Schedules) depuis l'application, un nouveau lancement d'INITIALIZE de l'actionneur EVENTS depuis l'interface eedomus viendra réinitialiser tous les paramètres au paramètrage de base ci-dessus.  
  

Influman 2018  
therealinfluman@gmail.com  
[Paypal Me](https://www.paypal.me/influman "paypal.me")  


  
   

  
   
   
  



 

 

  


