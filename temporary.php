<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!--JQUERY CDN-->
    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
    <!--AJAX CDN-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    <button id="dlBtn"> Download </button>

</body>
<script>
    var var1 = 'SAMPLE';

    var samplehtml = '<html><head><style>.parentDiv {display: flex;justify-content: center;} .parentDiv2 {display: flex;} .parentDiv3 {display: flex;justify-content: space-around;} img {height: 75px;margin-right: 50px;} h1 {font-size: 12px;} .companyh1 {font-size: 12px;} table {width: 100%;table-layout: fixed;} table,th,td {border: 1px solid black;border-collapse: collapse;} td {text-align: center;padding-top: 5px;padding-bottom: 5px;} </style></head>' +
        '<body><div id="pdfbody">' +
        '<h1 style="text-align: center; font-size: 20px;">' + var1 + '</h1>' +
        '<div class="parentDiv" style="border-bottom: 1px solid black; margin-bottom: 10px;"><div class="childDiv">' +
        '<img src="' + var1 + '"></div><div class="childDiv">' +
        '<h1 class="companyh1">' + var1 + '</h1>' +
        '<h1 class="companyh1">' + var1 + '</h1>' +
        '<h1 class="companyh1">' + var1 + '</h1>' +
        '<h1 class="companyh1">' + var1 + '</h1></div></div>' +
        '<div class="parentDiv2" style="margin-bottom: 30px;">' +
        '<div class="childDiv">' +
        '<h1>' + var1 + '</h1>' +
        '<h1>' + var1 + '</h1>' +
        '<h1>' + var1 + '</h1>' +
        '<h1>' + var1 + '</h1>' +
        '</div>' +
        '</div>' +
        '<div class="parentDiv2" style="margin-bottom: 30px;">' +
        '<div class="childDiv">' +
        '<h1>' + var1 + '</h1>' +
        '<h1>' + var1 + '</h1>' +
        '<h1>' + var1 + '</h1>' +
        '<h1>' + var1 + '</h1>' +
        '</div>' +
        '</div>' +
        '<div style="margin-bottom: 30px;">' +
        '<h1>This is to bill you for trucking service:</h1>' +
        '<table>' +
        '<tbody>' +
        '<tr>' +
        '<td class="table1TD">TRUCK COST:</td>' +
        '<td class="table1TD" id="truckCostTD">' + var1 + '</td>' +
        '</tr>' +
        '<tr>' +
        '<td class="table1TD">DROP FEE:</th>' +
        '<td class="table1TD" id="dropFeeTD">' + var1 + '</td>' +
        '</tr>' +
        '<tr>' +
        '<td class="table1TD">PARKING FEE:</td>' +
        '<td class="table1TD" id="parkingFeeTD">' + var1 + '</td>' +
        '</tr>' +
        '<tr>' +
        '<td class="table1TD">TOLL FEE:</td>' +
        '<td class="table1TD" id="tollFeeTD">' + var1 + '</td>' +
        '</tr>' +
        '<tr>' +
        '<td class="table1TD">FUEL CHARGE:</td>' +
        '<td class="table1TD" id="fuelChargeTD">' + var1 + '</td>' +
        '</tr>' +
        '<tr>' +
        '<td class="table1TD">EXTRA HELPER:</td>' +
        '<td class="table1TD" id="extraHelperTD">' + var1 + '</td>' +
        '</tr>' +
        '<tr>' +
        '<td class="table1TD">DEMURRAGE:</td>' +
        '<td class="table1TD" id="demurrageTD">' + var1 + '</td>' +
        '</tr>' +
        '<tr>' +
        '<td class="table1TD">OTHER MISC FEE:</td>' +
        '<td class="table1TD" id="miscFeeTD">' + var1 + '</td>' +
        '</tr>' +
        '<tr>' +
        '<td class="table1TD">SUBTOTAL:</td>' +
        '<td class="table1TD" id="subtotalTD">' + var1 + '</td>' +
        '</tr>' +
        '<tr>' +
        '<td class="table1TD">12% VAT:</td>' +
        '<td class="table1TD" id="vatTD">' + var1 + '</td>' +
        '</tr>' +
        '<tr>' +
        '<td class="table1TD">LESS PENALTIES:</td>' +
        '<td class="table1TD" id="penaltyTD">' + var1 + '</td>' +
        '</tr>' +
        '<tr>' +
        '<td class="table1TD">TOTAL TRUCKING CHARGES:</td>' +
        '<td class="table1TD" id="totalTD">' + var1 + '</td>' +
        '</tr>' +
        '</tbody>' +
        '</table>' +
        '<h1>TERMS: Balance due in 30 days.</h1>' +
        '</div>' +
        '<div class="parentDiv3">' +
        '<div class="childDiv" style="margin-right: 100px;">' +
        '<h1>Received by:_______________</h1>' +
        '</div>' +
        '<div class="childDiv">' +
        '<h1 style="margin-bottom: 50px;">________________________________________</h1>' +
        '<h1 style="text-align: center;">LOGISTIC MANAGER</h1>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</body>' +
        '</html>'

    var opt = {
        margin: 0.5,
        filename: 'invoice.pdf',
        image: {
            type: 'jpeg',
            quality: 1
        },
        html2canvas: {
            scale: 1
        },
        jsPDF: {
            unit: 'in',
            format: 'letter',
            orientation: 'portrait'
        }
    };

    html2pdf(samplehtml, opt);
</script>

</html>