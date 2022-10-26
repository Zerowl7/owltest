//Selector по body всего документа
let datatable = document.querySelector('.productsorderitems tbody');
let form = document.forms[0];


form.count.addEventListener('input', function (e) {
    let count = form.count.value;
    if (form.price.value) {
        form.cost.value = (form.price.value * count).toFixed(2);
        form.querySelector('.formcost').innerText = form.price.value;
        if (form.proc.value) {
            orderItemsCost();
        }
    }
})


function orderItemsCost() {
    form.cost.value = (form.cost.value - form.cost.value / 100 * form.proc.value).toFixed(2);
    form.discount.value = form.proc.value ? form.cost.value / 100 * form.proc.value : 0;
    form.querySelector('.formcost').innerText = form.cost.value;
}

let procs = document.querySelectorAll('.multiselect__option');

if (procs) {
    for (var i = 0; i < procs.length; i++) {
        procs[i].addEventListener('click', function (e) {
            orderItemsCost();
        });
    }
}

// действие по Клику для нажатия на tr
datatable.addEventListener('click', function (e) {
    let target = e.target.parentNode;
    let root_tr = target.closest('TR');

    let name = root_tr.cells[1].innerText;
    console.log(name);



    form.querySelector('.formname').innerText = name;
    form.product_id.value = root_tr.cells[0].innerText;
    form.price.value = root_tr.cells[2].innerText;
    form.querySelector('.formprice').innerText = form.price.value;

    document.querySelector("#nav-tab .seo").click();

    //  Admin.Messages.success('Сообщение', product_id);
});