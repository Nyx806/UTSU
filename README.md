# UTSU – Forum bienveillant

## Auteurs  
- **Hamdaoui Mayssa**  
- **Merlo Meyffren Antonin**

---

## 📌 Description

**UTSU** est un forum communautaire qui met l'accent sur la **bienveillance**, le **respect** et les **échanges intelligents**.  
Inspiré de Reddit, il propose diverses catégories et sous-forums où les utilisateurs peuvent :  
- publier du contenu (texte, images),
- commenter et répondre,
- liker ou disliker,
- suivre d'autres membres.

Le système de réputation valorise les comportements positifs :
- ✅ Les utilisateurs bienveillants obtiennent des **grades élevés** et sont **mis en avant** publiquement.
- ❌ Les comportements toxiques ou un mauvais usage du français peuvent mener à une **dégradation du statut**, voire un **bannissement**.

---

## ⚙️ Fonctionnalités

- Inscription et connexion des utilisateurs
- Création de posts avec texte et image
- Commentaires imbriqués (réponses aux commentaires)
- Système de like/dislike
- Suivi entre utilisateurs (follow)
- Attribution de grades selon le comportement
- Modération automatisée

---

## 🧩 Modèle de base de données (UML simplifié)

### `Users`
- `ID` (BIGINT, PK)  
- `username` (TEXT, NOT NULL)  
- `email` (TEXT, NOT NULL, UNIQUE)  
- `pp_img` (BLOB)  
- `password` (TEXT, NOT NULL)  
- `type` (INT, NOT NULL)  
- `statut` (INT, NOT NULL)

### `Categories`
- `ID` (BIGINT, PK)  
- `name` (TEXT, NOT NULL)

### `Posts`
- `ID` (BIGINT, PK)  
- `user_id` (BIGINT, FK → Users.ID, ON DELETE CASCADE)  
- `cat_id` (BIGINT, FK → Categories.ID, ON DELETE CASCADE)  
- `title` (TEXT, NOT NULL)  
- `contenu` (TEXT, NOT NULL)  
- `date` (DATETIME, NOT NULL, DEFAULT CURRENT_TIMESTAMP)  
- `photo` (BLOB)

### `Commentaires`
- `ID` (BIGINT, PK)  
- `id_post` (BIGINT, FK → Posts.ID, ON DELETE CASCADE)  
- `id_com_parent` (BIGINT, FK → Commentaires.ID, NULL)  
- `contenu` (TEXT, NOT NULL)  
- `img` (BLOB)  
- `video` (BLOB)  
- `date_creation` (DATETIME, NOT NULL, DEFAULT CURRENT_TIMESTAMP)

### `Likes`
- `ID` (BIGINT, PK)  
- `user_id` (BIGINT, FK → Users.ID, ON DELETE CASCADE)  
- `post_id` (BIGINT, FK → Posts.ID, ON DELETE CASCADE)  
- `cat_id` (BIGINT, FK → Categories.ID, ON DELETE CASCADE)  
- `type` (INT, NOT NULL)

### `Follow`
- `ID` (BIGINT, PK)  
- `utilisateur_ID` (BIGINT, FK → Users.ID, ON DELETE CASCADE)  
- `cible_ID` (BIGINT, FK → Users.ID, ON DELETE CASCADE)

### `Tableau`
- `ID` (BIGINT, PK)  
- `user_id` (BIGINT, FK → Users.ID, ON DELETE CASCADE)  
- `post_id` (BIGINT, FK → Posts.ID, ON DELETE CASCADE)  
- `cat_id` (BIGINT, FK → Categories.ID, ON DELETE CASCADE)  
- `type` (INT, NOT NULL)

---

## 📋 Méthodologie

Nous avons utilisé la **méthode Scrum Kanban**, en répartissant les tâches sous forme de cartes sur un tableau Trello/équivalent, avec une gestion des priorités, des statuts (à faire, en cours, terminé) et des itérations courtes.

---

## ✅ Conclusion

UTSU est une plateforme conçue pour créer un espace d’échange sain, où la qualité des interactions est centrale. Ce projet nous a permis d'approfondir nos compétences en conception logicielle, base de données, et développement web tout en portant une vision sociale forte : valoriser les comportements positifs dans les communautés en ligne.