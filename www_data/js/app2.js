/* eslint-disable no-undef */
/* eslint-disable no-unused-vars */

//List Drawers
async function drawersListCards(usuarioId,categoryId) {
  const $ = selector => document.querySelector(selector)
  const drawersList = $('#drawersList')
  const url = `./api/list-${usuarioId}-${categoryId}`
  const response = await fetch(url)
  const drawers = await response.json()
  let drawersListText = `<article class="row">`
  if(drawers.length){
    for(const drawer of drawers){
      drawersListText += `<section class="col card-300">
      <article class="card text-center shadow-${drawer.category_color}-md">
        <section class="card-body text-center">
        <article class="row"><section class="col"><h4><a href="drawer_view.php?id=${drawer.drawer_id}" class="text-decoration-none text-indigo">${drawer.drawer_name}</h4></a></section></article>
        <article class="row"><section class="col"><span class="badge rounded-pill bg-${drawer.category_color} ">${drawer.category_name}</span></section></article>
        <article class="row mt-3"><section class="col"><img src="images/drawers/${drawer.drawer_image}" class="img-fluid rounded-4 border border-2 border-${drawer.category_color} " width="120px" alt="${drawer.drawer_name}"></section></article>
        <article class="row mt-3">
        <section class="col text-center small fst-italic text-muted">${drawer.drawer_descriptinon}</section>
        </article>
        <article class="row mt-3">
        <section class="col-3"></section>
        <section class="col-3 d-grid gap-2"><a href="drawer_view.php?id=${drawer.drawer_id}" class="btn btn-outline-success"><i class="fa-regular fa-eye"></i></a></section>
        <section class="col-3 d-grid gap-2"><a href="drawer_del.php?id=${drawer.drawer_id}" class="btn btn-outline-danger"><i class="fa-solid fa-trash-can"></i></a></section>
        <section class="col-3"></section>
        </article>
        </section>
      </article>
      </section>`
    }
  }else{
    drawersListText += `<section class="col"><h2>No drawers to show</h2></section>`
  }

  drawersListText += `</article>`
  drawersList.innerHTML = drawersListText
}


async function drawersListTable(usuarioId,categoryId) {
  const url = `./api/list-${usuarioId}-${categoryId}`
  const drawersList = document.querySelector('#drawersList')

  drawersList.innerHTML = `<table id="drawersListTable" class="table table-sm table-hover" style="width:100%">
  <thead class="small">
    <th></th>
    <th>Name</th>
    <th>Category</th>
    <th>Description</th>
    <th>Content</th>
    <th>Total Items</th>
    <th>Drawer Price U$S</th>
    <th>Actions</th>
  </thead>
  <tbody class="small"></tbody>
</table>`

  const table = $('#drawersListTable').DataTable( {
    destroy: true,
    // language: {'url': '/dataTables/Spanish.json'},
    ajax: {'url': url,'dataSrc': ''},
    deferRender: true,
    stateSave: true,
    stateDuration: 120,
    pageLength: 20,
    order: [],
    paging: true,
    responsive: true,
    dom: 'Bfrtip',
    orderCellsTop: true,
    buttons: [ 
      { extend: 'pdf',orientation: 'landscape',pageSize: 'A4'},
      'print','excel',

    ],
    columns: [
      { 'data': 'drawer_image' , 'width': '10%' , className: 'text-center'},//0
      { 'data': 'drawer_name', 'width': '10%' },//1
      { 'data': 'category_name' , 'width': '10%' },//2
      { 'data': 'drawer_descriptinon' , 'width': '10%' },//3
      { 'data': 'items_included', 'width': '40%'  , className: 'text-start'},//4
      { 'data': 'items_total', className: 'text-center'},//5
      { 'data': 'drawer_price' , className: 'text-center'},//6
      { 'data': 'drawer_id' , className: 'text-center'},//7
    ],
    columnDefs: [
      {
        'targets': 0,
        'data': 'download_link',
        'render': function ( data, type, row ) {
          let srcIMG = 'default.png'
          if (row['drawer_image'].length > 0){srcIMG = `${row['drawer_image']}`}
          const respuesta =  `<img class="border border-${row['category_color']} mb-3 rounded-circle" src="images/drawers/${srcIMG}" alt="${row['drawer_name']}" width="100px" title="${row['drawer_name']}">`
          return respuesta
        }
      },
      {
        'targets': 1,
        'data': 'download_link',
        'render': function ( data, type, row ) {
          const respuesta =  `
          <a href="drawer_view.php?id=${row['drawer_id']}" class=" text-${row['category_color']}">${row['drawer_name']}</a><br>
          <span class="fst-italic text-muted small">(${row['drawer_location']})</span>
          `
          return respuesta
        }
      },
      {
        'targets': 2,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta =  `<span class="badge rounded-pill bg-${row['category_color']}">${row['category_name']}</span>`
          return respuesta
        }
      },
      {
        'targets': 3,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta =  `<span class="fst-italic text-muted">${row['drawer_descriptinon']}</span>`
          return respuesta
        }
      },
      {
        'targets': 4,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const drawerContent = row['items_included'] == null ? 'Empty':row['items_included']
          const respuesta = `<span class="small fst-italic text-muted">${drawerContent}</span>`
          return respuesta
        }
      },
      {
        'targets': 5,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta = row['items_total'] == null ? 0:row['items_total']
          // const respuesta = `<span class="small fst-italic text-muted">${drawerItems}</span>`
          return respuesta
        }
      },

      {
        'targets': 7,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta = `<a href="drawer_view.php?id=${row['drawer_id']}" class="btn btn-outline-success m-2" title="View ${row['drawer_name']}"><i class="fa-regular fa-eye"></i></a>
          <a href="drawer_del.php?id=${row['drawer_id']}" class="btn btn-outline-danger m-2" title="Delete ${row['drawer_name']}"><i class="fa-solid fa-trash-can"></i></a>`
          return respuesta
        }
      }
    ]
  })

}

// eslint-disable-next-line no-unused-vars
async function drawerView(drawerId) {
  const $ = selector => document.querySelector(selector)
  const drawer_title = $('#drawer_title')
  const drawer_image_full_Label = $('#drawer_image_full_Label')
  const drawer_name = $('#drawer_name')
  const drawer_location = $('#drawer_location')
  const drawer_image = $('#drawer_image')
  const drawer_image_full_src = $('#drawer_image_full_src')
  const drawer_descriptinon = $('#drawer_descriptinon')
  const drawer_category = $('#drawer_category')
  const drawer_card = $('#drawer_card')
  const drawer_card_items = $('#drawer_card_items')
  const url = `./api/view-${drawerId}`
  const url_category = `./api/categorylist-0`
  const response = await fetch(url)
  const drawer = await response.json()
  if(drawer.length != 0 ){
    drawer_title.innerHTML = drawer[0].drawer_name
    drawer_image_full_Label.innerHTML = drawer[0].drawer_name
    drawer_name.value = drawer[0].drawer_name
    drawer_location.value = drawer[0].drawer_location
    drawer_descriptinon.value = drawer[0].drawer_descriptinon
    drawer_card.classList.add(`shadow-${drawer[0].category_color}-blur`)
    drawer_card_items.classList.add(`shadow-${drawer[0].category_color}-blur`)
    drawer_image.classList.add(`border-${drawer[0].category_color}`)
    drawer_image.src = `images/drawers/${drawer[0].drawer_image}`
    drawer_image_full_src.src = `images/drawers/${drawer[0].drawer_image_full}`
    drawer_category.innerHTML=''
    const rtaCategories = await fetch(url_category)
    const listCategories = await rtaCategories.json()
    for(const category of listCategories){
      const selectedTag = category.category_id == drawer[0].drawer_category ? ' selected':''
      drawer_category.innerHTML += `<option class='text-${category.category_color}' value="${category.category_id}" ${selectedTag}>${category.category_name}</option>`
    }

    // for (let i = 0; i < drawer_category.options.length; i++) {
    //   if (drawer_category.options[i].value === drawer[0].drawer_category) {
    //     drawer_category.options[i].selected = true
    //     break
    //   }
    // }
  }else{
    // window.location.href ='index.php'
  }
}

// eslint-disable-next-line no-unused-vars
async function categoryList(selecDest) {
  const $ = selector => document.querySelector(selector)
  const selecDestOptions = $('#'+selecDest)
  let options = ``
  const url = `./api/categorylist-0`
  const response = await fetch(url)
  const categories = await response.json()
  for(const category of categories){
    options += `<option class='text-${category.category_color}' value="${category.category_id}">${category.category_name}</option>`
  }
  selecDestOptions.innerHTML = options
}

// eslint-disable-next-line no-unused-vars
async function drawerItems($drawerId, usuarioId) {
  const url = `./api/itemlist-${$drawerId}-${usuarioId}`
  // eslint-disable-next-line no-unused-vars, no-undef
  const table = $('#drawer_item_table').DataTable( {
    destroy: true,
    // language: {'url': '/dataTables/Spanish.json'},
    ajax: {'url': url,'dataSrc': ''},
    deferRender: true,
    stateSave: true,
    stateDuration: 120,
    pageLength: 15,
    order: [],
    paging: true,
    responsive: true,
    dom: 'Bfrtip',
    orderCellsTop: true,
    buttons: [
      { extend: 'pdf',orientation: 'landscape',pageSize: 'A4'},
      'print',
      'excel',

    ],
    columns: [
      { 'data': 'item_image' , className: 'text-center'},//0
      { 'data': 'item_name' },//1
      { 'data': 'category_name' },//2
      { 'data': 'item_descrption' },//3
      { 'data': 'item_amount' , className: 'text-end'},//4
      { 'data': 'item_price' , className: 'text-end'},//5
      { 'data': 'item_price' , className: 'text-end'},//6
      { 'data': 'item_id' , className: 'text-center'},//7
      { 'data': 'item_id' , className: 'text-center'},//8

    ],
    columnDefs: [

      {
        'targets': 0,
        'data': 'download_link',
        'render': function ( data, type, row) {
          let srcIMG = 'default.png'
          if (row['item_image'].length > 0){srcIMG = `${row['item_image']}`}
          const respuesta =  `<img class="border border-teal mb-3 rounded-circle" src="images/item/${srcIMG}" alt="" width="60px">`
          return respuesta
        }
      },
      {
        'targets': 2,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta =  `<span class="text-${row['category_color']}">${row['category_name']}</span>`
          return respuesta
        }
      },
      {
        'targets': 6,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta = row['item_amount'] * row['item_price']
          return respuesta.toFixed(2)
        }
      },
      {
        'targets': 7,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta = `<a href="item_view.php?id=${row['item_id']}&did=${row['item_drawer']}" class="btn btn-outline-success"><i class="fa-regular fa-eye"></i></a>`
          return respuesta
        }
      },
      {
        'targets': 8,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta = `<a href="item_del.php?id=${row['item_id']}" class="btn btn-outline-danger"><i class="fa-solid fa-trash-can"></i></a>`
          return respuesta
        }
      }
    ]
  })
}

// eslint-disable-next-line no-unused-vars
async function itemView(itemId,usuarioId) {
  const $ = selector => document.querySelector(selector)
  const item_amount = $('#item_amount')
  const item_card = $('#item_card')
  const item_category = $('#item_category')
  const item_descriptinon = $('#item_descriptinon')
  const item_drawer = $('#item_drawer')
  const item_image = $('#item_image')
  const item_image_full_Label = $('#item_image_full_Label')
  const item_image_full_src = $('#item_image_full_src')
  const item_name = $('#item_name')
  const item_price = $('#item_price')
  const item_title = $('#item_title')
  const searchImage = $('#searchImage')
  const searchML = $('#searchML')
  const url = `./api/itemview-${itemId}`
  const url_category = `./api/categorylist-0`
  const url_drawer = `./api/list-${usuarioId}-0`
  // const searchPdf = $('#searchPdf')

  const response = await fetch(url)
  const item = await response.json()
  if(item.length != 0 ){
    item_title.innerHTML = item[0].item_name
    item_image_full_Label.innerHTML = item[0].item_name
    item_name.value = item[0].item_name
    item_price.value = item[0].item_price
    item_amount.value = item[0].item_amount
    item_descriptinon.value = item[0].item_descrption
    item_card.classList.add(`shadow-${item[0].category_color}-blur`)
    item_image.classList.add(`border-${item[0].category_color}`)
    item_image.src = `images/item/${item[0].item_image}`
    item_image_full_src.src = `images/item/${item[0].item_image}`
    searchImage.href = `https://www.google.com/search?q=${item[0].item_descrption}&source=lnms&tbm=isch`
    searchML.href = `https://listado.mercadolibre.com.ar/${item[0].item_descrption}#D[A:${item[0].item_descrption}]`
    item_category.innerHTML = ''
    item_drawer.innerHTML = ''


    const rtaCategories = await fetch(url_category)
    const listCategories = await rtaCategories.json()
    for(const category of listCategories){
      const selectedTag = category.category_id == item[0].item_category ? ' selected':''
      item_category.innerHTML += `<option class='text-${category.category_color}' value="${category.category_id}" ${selectedTag}>${category.category_name}</option>`
    }

    const rtaDrawers = await fetch(url_drawer)
    const listDrawers = await rtaDrawers.json()
    for(const drawer of listDrawers ){
      const selectedTag = drawer.drawer_id == item[0].item_drawer ? ' selected':''
      item_drawer.innerHTML += `<option class='text-${drawer.category_color}' value="${drawer.drawer_id}" ${selectedTag}>${drawer.drawer_name}</option>`
    }
  }else{
    // window.location.href ='index.php'
  }
}

// eslint-disable-next-line no-unused-vars
async function drawerListSelect(selecDest,usuarioId) {
  const $ = selector => document.querySelector(selector)
  const selecDestOptions = $('#'+selecDest)
  const url = `./api/list-${usuarioId}-0`
  let options = ''
  const response = await fetch(url)
  const drawers = await response.json()
  if(drawers.length){
    for(const drawer of drawers){
      options += `<option class='text-${drawer.category_color}' value="${drawer.drawer_id}">${drawer.drawer_name}</option>`
    }
  }else{
    options += `<option value="0">No drawers to show</option>`
  }
  selecDestOptions.innerHTML = options
}

async function itemsAll(usuarioId,categoriaId) {
  const url = `./api/itemsall-${usuarioId}-${categoriaId}`
  // eslint-disable-next-line no-unused-vars, no-undef
  const table = $('#item_all_table').DataTable( {
    destroy: true,
    // language: {'url': '/dataTables/Spanish.json'},
    ajax: {'url': url,'dataSrc': ''},
    deferRender: true,
    stateSave: true,
    stateDuration: 120,
    pageLength: 20,
    order: [],
    paging: true,
    responsive: true,
    dom: 'Bfrtip',
    orderCellsTop: true,
    buttons: [
      { extend: 'pdf',orientation: 'landscape',pageSize: 'A4'},
      'print','excel',

    ],
    columns: [
      { 'data': 'item_image' , className: 'text-center'},//0
      { 'data': 'item_name' },//1
      { 'data': 'category_name' },//2
      { 'data': 'drawer_name' },//3
      { 'data': 'item_descrption' },//4
      { 'data': 'item_amount' , className: 'text-center'},//5
      { 'data': 'item_price' , className: 'text-center'},//6
      { 'data': 'item_id' , className: 'text-center'},//7
      { 'data': 'item_id' , className: 'text-center'},//8

    ],
    columnDefs: [

      {
        'targets': 0,
        'data': 'download_link',
        'render': function ( data, type, row) {
          let srcIMG = 'default.png'
          if (row['item_image'].length > 0){srcIMG = `${row['item_image']}`}
          const respuesta =  `<img class="border border-${row['category_color']} mb-3 rounded-circle" src="images/item/${srcIMG}" alt="" width="90px">`
          return respuesta
        }
      },
      {
        'targets': 1,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta =  `<a href="item_view.php?id=${row['item_id']}&did=${row['item_drawer']}" class="text-${row['category_color']}">${row['item_name']}</a>`
          return respuesta
        }
      },
      {
        'targets': 2,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta =  `<span class="badge rounded-pill bg-${row['category_color']}">${row['category_name']}</span>`
          return respuesta
        }
      },
      {
        'targets': 3,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta =  `<a href="drawer_view.php?id=${row['item_drawer']}" class="text-${row['category_color']}">${row['drawer_name']}</a>`
          return respuesta
        }
      },
      {
        'targets': 7,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta = `<a href="item_view.php?id=${row['item_id']}&did=${row['item_drawer']}" class="btn btn-outline-success"><i class="fa-regular fa-eye"></i></a>`
          return respuesta
        }
      },
      {
        'targets': 8,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta = `<a href="item_del.php?id=${row['item_id']}" class="btn btn-outline-danger"><i class="fa-solid fa-trash-can"></i></a>`
          return respuesta
        }
      }
    ]
  })
}

async function categoriesTable() {
  const url = `./api/categorylist-0`
  const table = $('#categoriesListTable').DataTable( {
    destroy: true,
    ajax: {'url': url,'dataSrc': ''},
    deferRender: true,
    stateSave: true,
    stateDuration: 120,
    pageLength: 20,
    order: [],
    paging: true,
    responsive: true,
    dom: 'Bfrtip',
    orderCellsTop: true,
    buttons: [
      { extend: 'pdf',orientation: 'landscape',pageSize: 'A4'},
      'print','excel',

    ],
    columns: [
      { 'data': 'category_name' },//0
      { 'data': 'category_color' },//1
      { 'data': 'DrawersPerCategory' , className: 'text-center'},//2
      { 'data': 'ItemsPerCategory' , className: 'text-center'},//3
      { 'data': 'DrawersPrice' , className: 'text-end'},//4
      { 'data': 'category_id' , className: 'text-center'},//5
    ],
    columnDefs: [
      {
        'targets': 0,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta =  `<a href="category_view.php?id=${row['category_id']}" class="text-${row['category_color']}">${row['category_name']}</a>`
          return respuesta
        }
      },
      {
        'targets': 1,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta =  `<span class="badge rounded-pill bg-${row['category_color']}">${row['category_color']}</span>`
          return respuesta
        }
      },
      {
        'targets': 2,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const DrawersPerCategory = row['DrawersPerCategory'] == null ? 0: row['DrawersPerCategory']
          const respuesta =  `<a href="index.php?id=${row['category_id']}" class="text-decoration-none">${DrawersPerCategory}</a>`
          return respuesta
        }
      },
      {
        'targets': 3,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const ItemsPerCategory = row['ItemsPerCategory'] == null ? 0: row['ItemsPerCategory']
          const respuesta =  `<a href="items.php?id=${row['category_id']}" class="text-decoration-none">${ItemsPerCategory}</a>`
          return respuesta
        }
      },
      {
        'targets': 4,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta = `$${row['DrawersPrice']}`
          return respuesta
        }
      },
      {
        'targets': 5,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta = `<a href="category_view.php?id=${row['category_id']}" class="btn btn-outline-success"><i class="fa-regular fa-eye"></i></a>`
          return respuesta
        }
      }
    ]
  })
}

// eslint-disable-next-line no-unused-vars
async function categoryView(categoryId) {
  console.log('(categoryId: ', categoryId)
  const $ = selector => document.querySelector(selector)
  const category_name = $('#category_name')
  const category_color = $('#category_color')
  const url = `./api/categoryview-${categoryId}`
  const response = await fetch(url)
  const category = await response.json()

  if(category.length != 0 ){
    category_name.value = category[0].category_name
    for (let i = 0; i < category_color.options.length; i++) {
      if (category_color.options[i].value === category[0].category_color) {
        category_color.options[i].selected = true
        break
      }
    }
  }
}

async function getStatistics(usuarioId,totalRecords) {
  const $ = selector => document.querySelector(selector)
  const statisticsCategoryPrice = $('#statisticsCategoryPrice')
  const statisticsPrice = $('#statisticsPrice')
  const statisticsCategoryTotal = $('#statisticsCategoryTotal')
  const urlPrice = `./api/totalprice-${totalRecords}`
  const responsePrice = await fetch(urlPrice)
  const priceToJson = await responsePrice.json()
  statisticsPrice.innerHTML = `<h1 class="text-white display-3"><i class="fa-regular fa-sack-dollar"></i>&nbsp;Full Value: <br>$${priceToJson[0].total}</h1>`

  const urlCategoryPrice  = `./api/categoryprice-${totalRecords}`
  const responseCategoryPrice = await fetch(urlCategoryPrice)
  const categoryPriceToJson = await responseCategoryPrice.json()
  let bodyTable = ''
  bodyTable = `<h5 class="text-white mb-3">Value By Category</h5><table class="bg-indigo text-white text-center" style="width:100%">
  <thead class="small"><th class="text-center" style="width:80%">Category</th><th class="text-center" colspan="2">Price U$S</th></thead><tbody class="small">`
  for(const fila of categoryPriceToJson){
    bodyTable  +=`<tr class="border-bottom"><td class="text-start">
    <a href='items.php?id=${fila.ID}' class="text-white text-decoration-none">${fila.Categoria}</a>
    </td><td class="text-start" style="width:5%">$</td><td class="text-end">
    <a href='items.php?id=${fila.ID}' class="text-white text-decoration-none">${fila.category_price}</a>
    </td></tr>`
  }
  statisticsCategoryPrice.innerHTML += `${bodyTable}</tbody></table>`

  const urlCategoryTotal  = `./api/categorytotal-${totalRecords}`
  const responseCategoryTotal = await fetch(urlCategoryTotal)
  const categoryTotalToJson = await responseCategoryTotal.json()

  bodyTable = `<h5 class="text-white mb-3">Items By Category</h5><table class="bg-indigo text-white text-center" style="width:100%">
  <thead class="small"><th class="text-center">Category</th><th class="text-center">Total Items</th></thead><tbody class="small">`
  for(const fila of categoryTotalToJson){
    bodyTable  +=`<tr class="border-bottom">
    <td class="text-start">
    <a href='items.php?id=${fila.ID}' class="text-white text-decoration-none">${fila.Categoria}</a>
    </td>
    <td class="text-end">
    <a href='items.php?id=${fila.ID}' class="text-white text-decoration-none">${fila.Total}</a>
    ${fila.Total}</td></tr>`
  }
  statisticsCategoryTotal.innerHTML += `${bodyTable}</tbody></table>`

}



(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    define([], factory)
  } else if (typeof module === 'object' && module.exports) {
    module.exports = factory()
  } else {
    root.mminch = factory()
  }
}(this, function () {

  function mminch (input) {
    const TO_FRACTION_64 = 0.015625
    const MM_TO_INCH = 25.4
    const INCH_TO_FEET = 12

    const simplifyFraction = function (numerator, _denominator) {
      const denominator = _denominator || 64
      // if there is no denominator then there is no fraction
      if (numerator < 1) {
        return ''
      }
      // fraction can't be broken further down:
      if (
        // a) if numerator is 1
        numerator === 1 ||
        // b) if numerator is prime number
        !((numerator % 2 === 0) || Math.sqrt(numerator) % 1 === 0)
      ) {
        return numerator + '/' + denominator
      }

      const newNumerator = numerator / 2
      const newDenominator = denominator / 2
      return simplifyFraction(newNumerator, newDenominator)
    }

    function toInch (_input) {
      const rawInches = Number(_input || input) / MM_TO_INCH
      // integers
      const integers = Math.floor(rawInches)
      // limit to 6 decimals to avoid conflicts
      const decimals = Number((rawInches % 1).toFixed(6))
      // fractionize for denominator 64
      const fraction64 = Math.round(decimals / TO_FRACTION_64)
      const simplifiedFraction = simplifyFraction(fraction64)
      const result = [integers, simplifiedFraction]
      return result.filter(function (r) { return r }).join(' ')
    }

    function toMM () {
      // should take numbers or strings
      const stringifiedInput = input + ''
      const fragments = stringifiedInput.split(' ')
      const inchesAndDecimals = fragments.map(function (fragment) {
        const broken = fragment.split('/')
        if (broken.length === 2) {
          // Strip the leading 0
          const decimals = (Number(broken[0]) / Number(broken[1])).toFixed(6)
          return decimals.slice(1)
        }
        return Number(broken[0])
      }).join('')

      // convert to mm
      const mm = Number(inchesAndDecimals) * MM_TO_INCH

      // return exact rounding if it is the 1:1 ratio
      // so the user doesn't freaks out of the conversion
      if (mm % MM_TO_INCH === 0) {
        return (Math.round(mm * 10) / 10) + ''
      }
      // round to the nearest half so it matches the toInch()
      // and return as string
      return (Math.round(mm * 2) / 2) + ''
    }

    function toFeet () {
      // parse to MM
      const mm = toMM()
      const inches = Math.round(mm / MM_TO_INCH)
      const feet = Math.floor(inches / INCH_TO_FEET)
      const stringFeet = feet + ' ft'
      const residualInches = Math.round(inches % INCH_TO_FEET)
      const stringInches = residualInches === 0 ? '' :
        ' ' + residualInches + ' in'

      if (!feet) {
        return stringFeet + ' ' + toInch(inches || 1) + ' in'
      }
      return stringFeet + stringInches
    }

    return {
      toInch: toInch,
      toFeet: toFeet,
      toMM: toMM
    }
  }
  return mminch
}))

function mmToFractionInches(mm) {
  const thousandthsOut = document.querySelector('#thousandthsOut')
  const fractionOut = document.querySelector('#fractionOut')
  const inches = mm / 25.4
  thousandthsOut.innerHTML = `Thousandths of an Inch: <span class="text-indigo">${inches.toFixed(3) } "<span class="text-indigo">`
  fractionOut.innerHTML = `Inches Fraction: <span class="text-indigo">${mminch(mm).toInch()} "<span class="text-indigo">`
}