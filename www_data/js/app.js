/* eslint-disable no-undef */
/* eslint-disable no-unused-vars */
//List Drawers
async function drawersListCards(usuarioId,categoryId) {
  const $ = selector => document.querySelector(selector)
  const drawersList = $('#drawersList')
  const url = `./api/api_drawers.php?id=list-${usuarioId}-${categoryId}`
   console.log(url)
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
        <section class="col-3"></section>pi/api_dra
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
  const url = `./api/api_drawers.php?id=list-${usuarioId}-${categoryId}`
  console.log(url)
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
      {extend:'copy',className: 'btn btn-darkblue',text:'<i class="fa-regular fa-copy"></i> Copy' },
      {extend: 'excel',className: 'btn btn-green',text:'<i class="fa-regular fa-file-excel"></i> Excel'},
      {extend:'pdf',className: 'btn btn-danger',text:'<i class="fa-regular fa-file-pdf"></i> Pdf',orientation: 'landscape',pageSize: 'A4'},
      {extend:'print',className: 'btn btn-indigo',text:'<i class="fa-regular fa-print"></i> Print'}
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
  const url_category = `./api/api_drawers.php?id=categorylist-0`
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

async function categoryList(selecDest) {
  const $ = selector => document.querySelector(selector)
  const selecDestOptions = $('#'+selecDest)
  let options = ``
  const url = `./api/api_drawers.php?id=categorylist-0`
  const response = await fetch(url)
  const categories = await response.json()
  for(const category of categories){
    options += `<option class='text-${category.category_color}' value="${category.category_id}">${category.category_name}</option>`
  }
  selecDestOptions.innerHTML = options
}

async function drawerItems($drawerId, usuarioId) {
  const url = `./api/itemlist-${$drawerId}-${usuarioId}`

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
      {extend:'copy',className: 'btn btn-darkblue',text:'<i class="fa-regular fa-copy"></i> Copy' },
      {extend: 'excel',className: 'btn btn-green',text:'<i class="fa-regular fa-file-excel"></i> Excel'},
      {extend:'pdf',className: 'btn btn-danger',text:'<i class="fa-regular fa-file-pdf"></i> Pdf',orientation: 'landscape',pageSize: 'A4'},
      {extend:'print',className: 'btn btn-indigo',text:'<i class="fa-regular fa-print"></i> Print'}
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
      // { 'width': '5%', 'targets': 0 },
      // { 'width': '5%', 'targets': 1 },
      // { 'width': '13%', 'targets': 2 },
      // { 'width': '13%', 'targets': 3 },
      // { 'width': '13%', 'targets': 4 },
      // { 'width': '13%', 'targets': 5 },
      // { 'width': '13%', 'targets': 6 },
      // { 'width': '13%', 'targets': 7 },
      // { 'width': '5%', 'targets': 8 },
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
  const item_brand = $('#item_brand')
  const item_model = $('#item_model')
  const searchImage = $('#searchImage')
  const searchML = $('#searchML')
  const url = `./api/itemview-${itemId}`
  const url_category = `./api/api_drawers.php?id=categorylist-0`
  const url_drawer = `./api/api_drawers.php?id=list-${usuarioId}-0`
  // const searchPdf = $('#searchPdf')
  const response = await fetch(url)
  const item = await response.json()
  if(item.length != 0 ){
    item_title.innerHTML = item[0].item_name
    item_image_full_Label.innerHTML = item[0].item_name
    item_name.value = item[0].item_name
    item_price.value = item[0].item_price
    item_amount.value = item[0].item_amount
    item_model.value = item[0].item_model
    item_descriptinon.value = item[0].item_descrption
    item_card.classList.add(`shadow-${item[0].category_color}-blur`)
    item_image.classList.add(`border-${item[0].category_color}`)
    item_image.src = `images/item/${item[0].item_image}`
    item_image_full_src.src = `images/item/${item[0].item_image}`
    searchImage.href = `https://www.google.com/search?q=${item[0].item_descrption}&source=lnms&tbm=isch`
    searchML.href = `https://listado.mercadolibre.com.ar/${item[0].item_descrption}_OrderId_PRICE_NoIndex_True`
    // searchML.href = `https://listado.mercadolibre.com.ar/${item[0].item_descrption}#D[A:${item[0].item_descrption}]`
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

    const urlBrands = `./api/api_drawers.php?id=brandlist-0`
    const rtaBrands = await fetch(urlBrands)
    const listBrands = await rtaBrands.json()
    for(const brand of listBrands ){
      const selectedTag = brand.brand_id== item[0].item_brand ? ' selected':''
      item_brand.innerHTML += `<option class='text-muted' value="${brand.brand_id}" ${selectedTag}>${brand.brand_name}</option>`
    }
    // $('#item_brand').select2({theme: 'bootstrap-5'})
    // $('#item_drawer').select2({theme: 'bootstrap-5' })
    // $('#item_category').select2({theme: 'bootstrap-5'})

  }else{
    // window.location.href ='index.php'
  }
}

async function drawerListSelect(selecDest,usuarioId) {
  const $ = selector => document.querySelector(selector)
  const selecDestOptions = $('#'+selecDest)
  const url = `./api/api_drawers.php?id=list-${usuarioId}-0`
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
  const url = `./api/api_drawers.php?id=itemsall-${usuarioId}-${categoriaId}`
  console.log('url: ', url)

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
      {extend:'copy',className: 'btn btn-darkblue',text:'<i class="fa-regular fa-copy"></i> Copy' },
      {extend: 'excel',className: 'btn btn-green',text:'<i class="fa-regular fa-file-excel"></i> Excel'},
      {extend:'pdf',className: 'btn btn-danger',text:'<i class="fa-regular fa-file-pdf"></i> Pdf',orientation: 'landscape',pageSize: 'A4'},
      {extend:'print',className: 'btn btn-indigo',text:'<i class="fa-regular fa-print"></i> Print'}
    ],
    columns: [
      // <th></th> 0
      { 'data': 'item_image' , className: 'text-center'},//0
      // <th>Name</th> 1
      { 'data': 'item_name' },//1
      // <th>Brand</th> 2
      { 'data': 'brand_name' , className: 'text-center'},//2
      // <th>Model</th> 3
      { 'data': 'item_model' , className: 'text-center'},//3
      // <th>Category</th> 4
      { 'data': 'category_name' },//4
      // <th>Drawer</th> 5
      { 'data': 'drawer_name' },//5
      // <th>Description</th> 6
      { 'data': 'item_descrption' },//6
      // <th>Amount</th> 7
      { 'data': 'item_amount' , className: 'text-center'},//7
      // <th>Price U$S</th> 8
      { 'data': 'item_price' , className: 'text-center'},//8
      // <th>Delete</th> 9
      { 'data': 'item_id' , className: 'text-center'},//9
    ],
    columnDefs: [
      { 'width': '5%', 'targets': 0 },
      { 'width': '10%', 'targets': 1 },
      { 'width': '7%', 'targets': 2 },
      { 'width': '7%', 'targets': 3 },
      { 'width': '7%', 'targets': 4 },
      { 'width': '5%', 'targets': 5 },
      // { 'width': '13%', 'targets': 6 },
      { 'width': '5%', 'targets': 7 },
      { 'width': '5%', 'targets': 8 },
      { 'width': '3%', 'targets': 9 },
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
          const respuesta =  `<div class="d-grid gap-2"><a href="item_view.php?id=${row['item_id']}&did=${row['item_drawer']}" class="text-${row['category_color']}">${row['item_name']}</a></div>`
          return respuesta
        }
      },
      {
        'targets': 4,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta =  `<span class="badge rounded-pill bg-${row['category_color']}">${row['category_name']}</span>`
          return respuesta
        }
      },
      {
        'targets': 5,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta =  `<a href="drawer_view.php?id=${row['item_drawer']}" class="text-${row['category_color']}">${row['drawer_name']}</a>`
          return respuesta
        }
      },
      {
        'targets': 9,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const respuesta = `<div class="d-grid gap-2"><a href="item_del.php?id=${row['item_id']}" class="btn btn-outline-danger"><i class="fa-solid fa-trash-can"></i></a></div>`
          return respuesta
        }
      }
    ]
  })
}
async function categoriesTable() {
  const url = `./api/api_drawers.php?id=categorylist-0`
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
      {extend:'copy',className: 'btn btn-darkblue',text:'<i class="fa-regular fa-copy"></i> Copy' },
      {extend: 'excel',className: 'btn btn-green',text:'<i class="fa-regular fa-file-excel"></i> Excel'},
      {extend:'pdf',className: 'btn btn-danger',text:'<i class="fa-regular fa-file-pdf"></i> Pdf',orientation: 'landscape',pageSize: 'A4'},
      {extend:'print',className: 'btn btn-indigo',text:'<i class="fa-regular fa-print"></i> Print'}
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
async function bookmarksTable(){
  const checkDeleted = document.getElementById('checkDeleted').checked
  const url = checkDeleted ? `./api/api_drawers.php?id=bookmarks-0`: `./api/api_drawers.php?id=bookmarksdel-0`
  console.log(url)
  const table = $('#bookmarksListTable').DataTable( {
    destroy: true,
    ajax: {'url': url,'dataSrc': ''},
    deferRender: true,
    stateSave: true,
    stateDuration: 120,
    pageLength: 30,
    order: [],
    paging: true,
    responsive: true,
    dom: 'Bfrtip',
    orderCellsTop: true,
    buttons: [
      {extend:'copy',className: 'btn btn-darkblue',text:'<i class="fa-regular fa-copy"></i> Copy' },
      {extend:'excel',className: 'btn btn-green',text:'<i class="fa-regular fa-file-excel"></i> Excel'},
      {extend:'pdf',className: 'btn btn-danger',text:'<i class="fa-regular fa-file-pdf"></i> Pdf',orientation: 'landscape',pageSize: 'A4'},
      {extend:'print',className: 'btn btn-indigo',text:'<i class="fa-regular fa-print"></i> Print'}
    ],
    columns: [
      // <th></th>
      { 'data': 'fav_img' },//0
      // <th>Title</th>
      { 'data': 'fav_title' },//1
      // <th>Title</th>
      { 'data': 'fav_desc' },//2
      // <th>Price</th>
      { 'data': 'fav_price' , className: 'text-end'},//3
      // <th>ML Id</th>
      { 'data': 'fav_mla' , className: 'text-end'},//4
      // <th>Update</th>
      { 'data': 'fav_date' , className: 'text-center'},//5
      // <th>Action</th>
      { 'data': 'fav_link' , className: 'text-center'},//6
    ],
    columnDefs: [
      {
        'targets': 0,
        'data': 'download_link',
        'render': function ( data, type, row) {
          let srcIMG = 'default.png'
          const fav_title = row['fav_title']
          if (row['fav_img'].length > 0){srcIMG = `${row['fav_img']}`}
          const respuesta =  `<a href="#item_image_full" data-bs-toggle="modal" onclick="viewImg('${srcIMG}','${fav_title}')"><img class="border border-orange mb-3 rounded-circle" src="${srcIMG}" alt="" width="90px"></a>`
          return respuesta
        }
      },
      {
        'targets': 1,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const fav_title = row['fav_title']
          const fav_delete = row['fav_delete']
          const color = fav_delete == 1 ? ' text-danger':''
          const fav_mla = row['fav_mla']
          const respuesta =  `<a href="#bookmark_modal_full" class="text-decoration-none ${color}" data-bs-toggle="modal" onclick="viewBookmark('${fav_mla}','${fav_title}')">${fav_title}</a>`
          return respuesta
        }
      },
      {
        'targets': 2,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const fav_desc = row['fav_desc']
          const fav_delete = row['fav_delete']
          const color = fav_delete == 1 ? ' text-danger':' text-muted'
          const respuesta =  `<span class="fst-italic ${color}" >${fav_desc}</span>`
          return respuesta
        }
      },
      {
        'targets': 3,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const fav_delete = row['fav_delete']
          const color = fav_delete == 1 ? ' text-danger':''
          const fav_price = row['fav_price']
          return `<span class="${color}">$ ${fav_price}</span>`
        }
      },
      {
        'targets': 5,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const fav_delete = row['fav_delete']
          const color = fav_delete == 1 ? ' text-danger':''
          const fav_date = row['fav_date']
          return `<span class="${color}">$ ${fav_date}</span>`
        }
      },
      {
        'targets': 6,
        'data': 'download_link',
        'render': function ( data, type, row) {
          const fav_title = row['fav_title']
          const fav_delete = row['fav_delete']
          const fav_link = row['fav_link']
          const fav_mla = row['fav_mla']
          const linkML = `https://listado.mercadolibre.com.ar/${fav_title}_OrderId_PRICE_NoIndex_True`
          const respuesta =  fav_delete == 0 ? `<a href="${fav_link}" class="btn btn-outline-indigo" target="_blank"><i class="fa-regular fa-arrow-up-right-from-square"></i></a>
          <a href="javascript:void(0)" class="btn btn-outline-danger" onclick="deleteBookmark('${fav_mla}')"><i class="fa-regular fa-trash"></i></a>
          <a href="${linkML}" class="btn btn-outline-primary" target="_blank"><i class="fa-regular fa-magnifying-glass"></i></a>`:`<a href="${fav_link}" class="btn btn-outline-indigo" target="_blank"><i class="fa-regular fa-arrow-up-right-from-square"></i></a>
          <a href="javascript:void(0)" class="btn btn-outline-success" onclick="restoreBookmark('${fav_mla}')"><i class="fa-regular fa-trash-can-arrow-up"></i></a>
          <a href="${linkML}" class="btn btn-outline-primary" target="_blank"><i class="fa-regular fa-magnifying-glass"></i></a>`
          return respuesta
        }
      },
    ]
  })
  priceBookmark()
}

async function categoryView(categoryId) {
  const $ = selector => document.querySelector(selector)
  const category_name = $('#category_name')
  const category_color = $('#category_color')
  const url = `./api/api_drawers.php?id=categoryview-${categoryId}`
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
  const urlPrice = `./api/api_drawers.php?id=totalprice-${totalRecords}`
  const responsePrice = await fetch(urlPrice)
  const priceToJson = await responsePrice.json()
  statisticsPrice.innerHTML = `<h1 class="text-white display-3"><i class="fa-regular fa-sack-dollar"></i>&nbsp;Full Value: <br>$${priceToJson[0].total}</h1>`
  const urlCategoryPrice  = `./api/api_drawers.php?id=categoryprice-${totalRecords}`
  const responseCategoryPrice = await fetch(urlCategoryPrice)
  const categoryPriceToJson = await responseCategoryPrice.json()
  let bodyTable = ''
  bodyTable = `<h5 class="text-white mb-3">Value By Category</h5><table class="bg-indigo text-white text-center" style="width:100%">
  <thead class="small"><th class="text-start" style="width:80%">Category</th><th class="text-end" colspan="2">Price U$S</th></thead><tbody class="small">`
  for(const fila of categoryPriceToJson){
    bodyTable  +=`<tr class="border-bottom"><td class="text-start">
    <a href='items.php?id=${fila.ID}' class="text-white text-decoration-none">${fila.Categoria}</a>
    </td><td class="text-start" style="width:5%">$</td><td class="text-end">
    <a href='items.php?id=${fila.ID}' class="text-white text-decoration-none">${fila.category_price}</a>
    </td></tr>`
  }
  statisticsCategoryPrice.innerHTML += `${bodyTable}</tbody></table>`
  const urlCategoryTotal  = `./api/api_drawers.php?id=categorytotal-${totalRecords}`
  const responseCategoryTotal = await fetch(urlCategoryTotal)
  const categoryTotalToJson = await responseCategoryTotal.json()
  bodyTable = `<h5 class="text-white mb-3">Items By Category</h5>
  <table class="bg-indigo text-white text-center" style="width:100%">
  <thead class="small">
  <th class="text-start">Category</th><th class="text-end">Total Items</th>
  </thead><tbody class="small">`
  for(const fila of categoryTotalToJson){
    bodyTable  +=`<tr class="border-bottom">
    <td class="text-start"><a href='items.php?id=${fila.ID}' class="text-white text-decoration-none">${fila.Categoria}</a></td>
    <td class="text-end"><a href='items.php?id=${fila.ID}' class="text-white text-decoration-none">${fila.Total}</a></td></tr>`
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
async function deleteBookmark(article) {
  const url = `./api/api_drawers.php?id=deleteBookmark-${article}`
  const response = await fetch(url)
  bookmarksTable()
}
async function restoreBookmark(article) {
  const url = `./api/rapi_drawers.php?id=estoreBookmark-${article}`
  const response = await fetch(url)
  bookmarksTable()
}
async function clearBookmark() {
  const url = `./api/api_drawers.php?id=clearBookmark-0`
  const response = await fetch(url)
  bookmarksTable()
}
async function priceBookmark() {
  const totalPriceBookmark = document.querySelector('#totalPriceBookmark')
  const url = `./api/api_drawers.php?id=bookmarks-0`
  const responsePrice = await fetch(url)
  const bookmarks =  await responsePrice.json()
  let totalPrice = 0
  for(const book of bookmarks){
    totalPrice +=  Number(book.fav_price)
  }
  totalPriceBookmark.innerHTML = `Mercado Libre Bookmarks <span class="fst-italic text-muted"> (${totalPrice.toLocaleString('es-AR', {style: 'currency', currency: 'ARS'})})</span> `
}
async function viewImg(imagen,title) {
  const item_image_full_Label = document.querySelector('#item_image_full_Label')
  const item_image_full_src = document.querySelector('#item_image_full_src')
  item_image_full_src.src = ''
  item_image_full_src.src = imagen
  item_image_full_Label.innerHTML = title
}
async function viewBookmark(bookmarkID,title) {
  const bookmark_modal_full_Label = document.querySelector('#bookmark_modal_full_Label')
  const bookmarkIDHidden = document.querySelector('#bookmarkID')
  const bookmarkTitle = document.querySelector('#bookmarkTitle')
  bookmarkIDHidden.value =  bookmarkID
  bookmark_modal_full_Label.innerHTML = title
  bookmarkTitle.value = title
}

async function addItemAList(){
  const $ = selector => document.querySelector(selector)
  const newItemName = $('#newItemName').value
  const item_brand = $('#item_brand')
  const urlAddItem = `./brand_save.php`

  const responseNewItem = await fetch(urlAddItem, {
    method: 'POST',
    body: JSON.stringify({newItem:newItemName})
  })
  const newItemID =  await responseNewItem.json()
  console.log('newItemID: ', newItemID)
  item_brand.innerHTML = ''
  const urlBrands = `./api/api_drawers.php?id=brandlist-0`
  const rtaBrands = await fetch(urlBrands)
  const listBrands = await rtaBrands.json()
  for(const brand of listBrands ){
    const selectedTag = brand.brand_id== newItemID[0].newItemID ? ' selected':''
    item_brand.innerHTML += `<option class='text-muted' value="${brand.brand_id}" ${selectedTag}>${brand.brand_name}</option>`
  }

}
