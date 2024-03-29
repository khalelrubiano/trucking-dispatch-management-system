
const editHeader = document.getElementById('editHeader');
const editHeader2 = document.getElementById('editHeader2');

let allTabLink = document.getElementById('allTabLink');
let settledTabLink = document.getElementById('settledTabLink');
let unsettledTabLink = document.getElementById('unsettledTabLink');


let allTabLi = document.getElementById('allTabLi');
let settledTabLi = document.getElementById('settledTabLi');
let unsettledTabLi = document.getElementById('unsettledTabLi');


const arrayLengthHidden = document.getElementById('arrayLengthHidden');
const ancestorTile = document.getElementById('ancestorTile');
const selectSort = document.getElementById('selectSort');
const test_indicator = document.getElementById('test_indicator');
let indicator = document.getElementById('indicator')
let searchBarInput = document.getElementById('searchBarInput')
let currentPageNumber = 1;

let tabValueHidden = document.getElementById('tabValueHidden')

let logList = document.getElementById('logList')

let addModal = document.getElementById('addModal')
let typeAdd = document.getElementById('typeAdd')
let invoiceNumberAdd = document.getElementById('invoiceNumberAdd')
let invoiceNumberAddTemp = '';
let vehicleAdd = document.getElementById('vehicleAdd')
let vehicleAddField = document.getElementById('vehicleAddField')

let pdfModal = document.getElementById('pdfModal')
let invoiceNumberPDF = document.getElementById('invoiceNumberPDF')
let invoiceNumberPDFTemp = '';
let submitPDFForm = document.getElementById('submitPDFForm')

let submitAddForm = document.getElementById('submitAddForm')

function generatePayslipList1(tabValueVar, orderByVar) {
    $.post("./classes/load-payslip-individual.class.php", {
        tabValue: tabValueVar,
        orderBy: orderByVar
    }, function (data) {

        var jsonArray = JSON.parse(data);

        //alert('data');


        var newParentTile = document.createElement("div");
        newParentTile.classList.add('tile');
        newParentTile.classList.add('is-parent');
        newParentTile.classList.add('is-vertical');
        newParentTile.setAttribute("style", "align-items: center;");
        ancestorTile.appendChild(newParentTile);

        //load billing into array 1
        for (var i = 0; i < jsonArray.length; i++) {

            //GENERATE SHIPMENT DETAILS HERE
            var newChildTile = document.createElement("div");
            newChildTile.classList.add('tile');
            newChildTile.classList.add('is-child');
            newChildTile.classList.add('p-2');
            newChildTile.classList.add('is-6');

            //CARD
            var newCard = document.createElement("div");
            newCard.classList.add('card');
            newChildTile.appendChild(newCard);

            //CARD HEADER

            var newCardHeader = document.createElement("header");
            newCardHeader.classList.add('card-header');
            newCard.setAttribute("style", "border-radius: 5%;");
            newCard.appendChild(newCardHeader);

            //CARD HEADER PARAGRAPH
            var newCardHeaderParagraph = document.createElement("p");
            newCardHeaderParagraph.classList.add('card-header-title');

            var newCardHeaderParagraphIcon = document.createElement("i");
            newCardHeaderParagraphIcon.classList.add('fa-solid');
            newCardHeaderParagraphIcon.classList.add('fa-circle');
            newCardHeaderParagraphIcon.classList.add('mr-3');

            newCardHeaderParagraph.appendChild(newCardHeaderParagraphIcon);

            switch (jsonArray[i][0]) {
                case "Settled":
                    newCardHeaderParagraph.classList.add('has-text-primary');
                    newCardHeaderParagraph.innerHTML = "<i class='fa-solid fa-circle-check mr-3 has-text-primary'></i>" + jsonArray[i][0] + " - " + jsonArray[i][2];
                    newCardHeader.appendChild(newCardHeaderParagraph);
                    break;
                case "Unsettled":
                    newCardHeaderParagraph.classList.add('has-text-warning');
                    newCardHeaderParagraph.innerHTML = "<i class='fa-solid fa-circle-exclamation mr-3 has-text-warning'></i>" + jsonArray[i][0];
                    newCardHeader.appendChild(newCardHeaderParagraph);
                    break;

            }

            //CARD CONTENT
            var newCardContent = document.createElement("div");
            newCardContent.classList.add('card-content');
            newCard.appendChild(newCardContent);

            //CARD CONTENT MEDIA
            var newContent = document.createElement("div");
            newContent.classList.add('content');
            newCardContent.appendChild(newContent);


            //CONTENT TABLE
            var newContentTable = document.createElement("table");
            newContentTable.classList.add('table');
            newContentTable.classList.add('is-bordered');
            newContent.appendChild(newContentTable);

            //CONTENT TABLE TBODY
            var newContentTableTbody = document.createElement("tbody");
            newContentTable.appendChild(newContentTableTbody);

            //CONTENT TABLE TBODY TR 1
            var newContentTableTbodyTr1 = document.createElement("tr");
            newContentTableTbody.appendChild(newContentTableTbodyTr1);

            var newContentTableTbodyTr1Td1 = document.createElement("td");
            newContentTableTbodyTr1Td1.classList.add('has-text-weight-bold');
            newContentTableTbodyTr1Td1.innerHTML = "Invoice Number:";
            newContentTableTbodyTr1.appendChild(newContentTableTbodyTr1Td1);

            var newContentTableTbodyTr1Td2 = document.createElement("td");
            newContentTableTbodyTr1Td2.innerHTML = jsonArray[i][3];
            newContentTableTbodyTr1.appendChild(newContentTableTbodyTr1Td2);

            //CONTENT TABLE TBODY TR 2
            var newContentTableTbodyTr2 = document.createElement("tr");
            newContentTableTbody.appendChild(newContentTableTbodyTr2);

            var newContentTableTbodyTr2Td1 = document.createElement("td");
            newContentTableTbodyTr2Td1.classList.add('has-text-weight-bold');
            newContentTableTbodyTr2Td1.innerHTML = "Plate Number:";
            newContentTableTbodyTr2.appendChild(newContentTableTbodyTr2Td1);

            var newContentTableTbodyTr2Td2 = document.createElement("td");
            newContentTableTbodyTr2Td2.innerHTML = jsonArray[i][1];
            newContentTableTbodyTr2.appendChild(newContentTableTbodyTr2Td2);

            //CONTENT TABLE TBODY TR 3
            var newContentTableTbodyTr3 = document.createElement("tr");
            newContentTableTbody.appendChild(newContentTableTbodyTr3);

            var newContentTableTbodyTr3Td1 = document.createElement("td");
            newContentTableTbodyTr3Td1.classList.add('has-text-weight-bold');
            newContentTableTbodyTr3Td1.innerHTML = "Owner:";
            newContentTableTbodyTr3.appendChild(newContentTableTbodyTr3Td1);

            var newContentTableTbodyTr3Td2 = document.createElement("td");
            newContentTableTbodyTr3Td2.innerHTML = jsonArray[i][4] + " " + jsonArray[i][5] + " " + jsonArray[i][6];
            newContentTableTbodyTr3.appendChild(newContentTableTbodyTr3Td2);

            //CONTENT TABLE TBODY TR 4
            var newContentTableTbodyTr4 = document.createElement("tr");
            newContentTableTbody.appendChild(newContentTableTbodyTr4);

            var newContentTableTbodyTr4Td1 = document.createElement("td");
            newContentTableTbodyTr4Td1.classList.add('has-text-weight-bold');
            newContentTableTbodyTr4Td1.innerHTML = "Date:";
            newContentTableTbodyTr4.appendChild(newContentTableTbodyTr4Td1);

            var newContentTableTbodyTr4Td2 = document.createElement("td");
            newContentTableTbodyTr4Td2.innerHTML = jsonArray[i][7];
            newContentTableTbodyTr4.appendChild(newContentTableTbodyTr4Td2);

            //CARD CONTENT MEDIA-CONTENT SUBTITLE
            var newCardFooter = document.createElement("footer");
            newCardFooter.classList.add('card-footer');
            newCard.appendChild(newCardFooter);

            //CARD CONTENT MEDIA-CONTENT SUBTITLE ( NEEDS HREF )
            var newCardFooterLink = document.createElement("a");
            newCardFooterLink.setAttribute("onclick", "redirectToPayslipProfile('" + jsonArray[i][8] + "','" + jsonArray[i][0] + "')");
            newCardFooterLink.classList.add('card-footer-item');
            newCardFooterLink.innerHTML = "View Details";
            newCardFooter.appendChild(newCardFooterLink);

            var newCardFooterLink2 = document.createElement("a");
            newCardFooterLink2.setAttribute("onclick", "deleteAjax('" + jsonArray[i][8] + "')");
            newCardFooterLink2.classList.add('card-footer-item');
            newCardFooterLink2.innerHTML = "<i class='fa-solid fa-trash-can p-1 mr-1'></i> Delete";
            newCardFooterLink2.classList.add('has-text-danger');
            newCardFooter.appendChild(newCardFooterLink2);

            //newChildTile.innerHTML = "entry number: " + jsonArray[i - 1][0];
            newParentTile.appendChild(newChildTile);

        };

    });
}
//alert(tabValueHidden.innerHTML);

generatePayslipList1(tabValueHidden.innerHTML, selectSort.value);

function redirectToPayslipProfile(payrollIdVar, payrollStatusHiddenVar) {
    $.post("./classes/set-payslip-session-variable.class.php", {
        payrollId: payrollIdVar,
        payrollStatusHidden: payrollStatusHiddenVar
    }, function (data) {
        //var jsonArray = JSON.parse(data);
        //alert("success call");
        window.location.href = "payslip-profile-individual.php";
    });
}

allTabLink.addEventListener('click', () => {
    settledTabLi.classList.remove('is-active');
    unsettledTabLi.classList.remove('is-active');

    allTabLi.classList.add('is-active');

    tabValueHidden.innerHTML = "All";
    ancestorTile.innerHTML = "";
    currentPageNumber = 1;

    //alert(tabValueHidden.innerHTML);
    generatePayslipList1(tabValueHidden.innerHTML, selectSort.value);
});

settledTabLink.addEventListener('click', () => {
    allTabLi.classList.remove('is-active');
    unsettledTabLi.classList.remove('is-active');

    settledTabLi.classList.add('is-active');

    tabValueHidden.innerHTML = "Settled";
    ancestorTile.innerHTML = "";
    currentPageNumber = 1;
    //alert(tabValueHidden.innerHTML);
    generatePayslipList1(tabValueHidden.innerHTML, selectSort.value);
});

unsettledTabLink.addEventListener('click', () => {
    allTabLi.classList.remove('is-active');
    settledTabLi.classList.remove('is-active');

    unsettledTabLi.classList.add('is-active');

    tabValueHidden.innerHTML = "Unsettled";
    ancestorTile.innerHTML = "";
    currentPageNumber = 1;
    //alert(tabValueHidden.innerHTML);
    generatePayslipList1(tabValueHidden.innerHTML, selectSort.value);
});

selectSort.addEventListener('change', () => {

    indicator.innerHTML = selectSort.value;
    ancestorTile.innerHTML = "";
    currentPageNumber = 1;
    generatePayslipList1(tabValueHidden.innerHTML, selectSort.value);

});