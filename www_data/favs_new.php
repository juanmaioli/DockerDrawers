<?php
include("head.php");
?>

<!-- Container -->
<main class="container-fluid">
  <article class="row ms-2 me-2">
    <section class="col">
      <article class="card shadow-indigo-sm">
        <section class="card-header">
          <article class="row">
            <section class="col-md-3 text-start">
              <h3 class="text-indigo">Paste HTML Code</h3>
            </section>
          </article>
        </section>
        <section class="card-body" id="newCode">
        <textarea class="form-control mb-3" id="code" rows="22"></textarea>
        <button class="btn btn-indigo" onclick="process()">Process</button>
        </section>
      </article>
    </section>
  </article>

</main>
<!-- /Container -->
<?php include("footer.php"); ?>
<script>

async function process(){
  const newCode = document.querySelector('#newCode')
  const code = document.querySelector('#code').value
  const newUl = document.createElement("div")

  newCode.innerHTML = '<a href="javascript:(void)" onclick="procesaFavs()" class="btn btn-indigo"> Save</a>'
  newUl.innerHTML = code
  newCode.appendChild(newUl)
  procesaFavs()

}

async function procesaFavs() {
  // const favoritos = document.querySelectorAll('.ui-list__list-item')
  const favoritos = document.querySelectorAll('.build-list__list-item')
  const listaUL = document.querySelector('ul')
  const newCode = document.querySelector('#newCode')
  let tablaFinal = '<table id="tablaFavoritos"><thead><tr><th>N°</th><th>Image</th><th>Item</th><th>Precio</th><th>Link</th></tr></thead><tbody>'
  let contador = 0
  const listaJSON = []

  const arrayItems = Array.from(favoritos)
  console.log('favoritos: ', favoritos.length)
  favoritos.forEach(function (listItem) {
    contador++
    const article = listItem.querySelector('.poly-action-links__action')
    const articleInput = article.querySelector('input')
    const articleID = articleInput.value
    const cardTitle = listItem.querySelector('.poly-component__title')
    const imageCard = listItem.querySelector('.poly-card__portada')
    const image = imageCard.querySelector('.poly-component__picture')
    const link = cardTitle.getAttribute('href')
    const linkTag = `<a href="${cardTitle.getAttribute('href')}" target="_blank">Ver</a>`
    const price = listItem.querySelector('.poly-component__price')
    const fractionElement = price.querySelector('.andes-money-amount__fraction')
    const image1 = image.src == 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' || image.src == null ? '': image.src
    const image2 = image.getAttribute('data-src') == null ? '': image.getAttribute('data-src')
    const precio = Number(fractionElement.textContent.replace('.',''))
    const title = cardTitle.textContent.replace('\n',' ')
    const imageFinal = image1 == '' ? image2:image1
    const imageFinalTag = `<img width="200" height="200" src="${imageFinal}" alt="">`
    tablaFinal += `<tr><td>${contador}</td><td>${imageFinalTag}</td><td>${title}</td><td>${precio}</td><td>${linkTag}</td></tr>`
    const queryFinal = {
      titulo:title.replace('           ',' '),
      link:link,
      precio:precio,
      imagen:imageFinal,
      mlaID:articleID.split(',')[0]
    }
    listaJSON.push(queryFinal)
  })
  tablaFinal += '</tbody></table>'
  newCode.innerHTML = ''
  newCode.innerHTML = tablaFinal

  const url = 'favs_save.php'
  const response = await fetch(url, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(listaJSON)
    })
    const responseText = await response.json();
    console.log('responseText: ',  responseText.data)

    if(responseText.status == 'ok' ){
      window.location.href = "favs.php"
    }
}
</script>