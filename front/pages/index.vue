<template>
    <div>
        <div class="title">
            <h1>Game Stackers</h1>
            <p> Lorem ipsum, dolor sit amet consectetur adipisicing elit.</p>
        </div>

        <section>
            <h2>Nos derniers articles</h2>
            <!-- Faire une boucle sur toutes les catégories d'articles -->
            <div class="categories">
                <button>Catégorie 1</button>
                <button>Catégorie 2</button>
                <button>Catégorie 3</button>
                <button>Catégorie 4</button>
            </div>
            <div class="articles">
                <div class="article">
                    <img src="/medias/mario.webp" alt="">
                    <h3>Titre de l'article</h3>
                    <p>Contenu de l'article</p>
                </div>
                <div class="article">
                    <img src="/medias/mario.webp" alt="">
                    <h3>Titre de l'article</h3>
                    <p>Contenu de l'article</p>
                </div>
                <div class="article">
                    <img src="/medias/mario.webp" alt="">
                    <h3>Titre de l'article</h3>
                    <p>Contenu de l'article</p>
                </div>
                <div class="article">
                    <img src="/medias/mario.webp" alt="">
                    <h3>Titre de l'article</h3>
                    <p>Contenu de l'article</p>
                </div>
                <div class="article">
                    <img src="/medias/mario.webp" alt="">
                    <h3>Titre de l'article</h3>
                    <p>Contenu de l'article</p>
                </div>
            </div>
        </section>
    </div>
</template>

<style>
.title {
    text-align: center;
    margin: 20px 0;
}

.categories{
    display: flex;
    gap: 10px;
    justify-content: center;
    margin: 20px 0;

    button{
        padding: 10px 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        background-color: #f1f1f1;
        cursor: pointer;
    }
}
.articles {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: space-between;

    .article {
        flex: 1 1 calc(33.333% - 20px);
        box-sizing: border-box;
        min-width: 250px;
        max-width: 300px;
        border: 1px solid #ccc;
        border-radius: 10px;

        img{
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        };

        h3,p{
            padding: 10px;
        }
    }
    
}


@media (max-width: 768px) {
    .article {
        flex: 1 1 calc(45% - 20px);
        max-width: 45%;
    }
}

@media (max-width: 480px) {
    .article {
        flex: 1 1 calc(100% - 20px);
    }
}
</style>
