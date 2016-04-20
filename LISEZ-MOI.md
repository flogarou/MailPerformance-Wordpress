MailPerformance
==
Contributeurs: NP6

License: MIT License

Ce plugin permet a votre systeme de d'incription d'ajouter une nouvelle cible a votre compte MailPerformance.

Description
--

Les principales caracteristiques de MailPerformance comprennent:

* Automatisation de l'ajout d'une cible a votre compte MailPerformance lorsqu'une cible s'inscrit a votre WordPress.
* Envoi d'un mail automatique a l'administrateur/support du blog en cas de probleme.

PS: Vous avez besoin de votre 'x-Key' et de votre 'Id Champ' (le champ doit etre de type 'E-mail' et avoir le 'Critere d'unicite') pour pouvoir activer le plugin. (Si vous n'avez pas votre x-Key, vous pouvez envoyer un email a : "apiv8@np6.com")

Installation
--

Telecharger le plugin avec le MailPerformancePlugin.zip.

Aller dans extensions -> Ajouter -> Mettre une extension en ligne -> Choisissez un fichier -> Selectionner le fichier 'MailPerformance.zip' -> Installer maintenant.

Activez le plugin dans les extensions, puis rentrez votre 'x-Key' et 'Id Champ' (le champ doit etre de type 'E-mail' et avoir le 'Critere d'unicite') dans l'onglet 'MailPerformance'.

Dans votre plugin d'inscription, quand un utilisateur s'inscrit, vous devez appeler la fonction "MPerf_Plugin::MPerfPostTarget([email]);" (PHP) avec l'email de l'utilisateur. (Pour plus d'aide, demandez a : "apiv8@np6.com")

Vous pouvez appeler cette fonction en PHPgrâce à un simple appel GET sur la route : '/wp-admin/admin-ajax.php?email=[EMAIL]'.

1, 2, 3: C'est fait!

=== Fin ===

Contact
--

Contactez-nous sur : http://www.np6.fr/demande-de-contact/
