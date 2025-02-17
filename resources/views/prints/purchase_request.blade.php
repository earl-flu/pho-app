<!-- resources/views/reports/issued_drugs.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Request - {{ $purchaseRequest->name }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
        }

        span.peso {
            font-family: 'DejaVu Sans', sans-serif;
        }

        .header {
            border: 1px solid black;
            padding: 10px;
        }

        .header-row {
            /* overflow: hidden; */
            height: 20px;
            margin-top: 20px;
        }

        .header-h2 {
            text-align: center
        }

        .header-col {
            float: left;
            width: 50%;
        }

        .header-text {
            border-bottom: 1px solid black;
            min-width: 180px;
            display: inline-block;
            margin-bottom: -3px;
        }

        .details-div {
            /* overflow: hidden; */
            height: 75px;
            border: 1px solid black;
            border-top: none;
            padding: 10px;
        }

        .details-col {
            float: left;
        }

        .details-col-1 {
            width: 35%;
        }

        .details-col-1 .underline {
            min-width: 120px;
            border-bottom: 1px solid black;
        }

        .details-col-2 {
            width: 45%;
        }

        .details-col-2 .underline {
            min-width: 160px;
            border-bottom: 1px solid black;
        }

        .details-col-3 {
            width: 20%;
        }

        .details-col-3 .underline {
            min-width: 90px;
            border-bottom: 1px solid black;
        }

        .pt-5 {
            padding-top: 6px;
        }

        .table-div {
            padding: 10px;
            border: 1px solid black;
            min-height: 500px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th:last-child {
            border-right: 1px solid black;
            /* Ensure the right border on the last <th> is visible */
        }

        .items-table th {
            background-color: #cdcdcd;
            padding: 10px;
            border: 1px solid black;
        }

        .items-table td {
            border: 1px solid black;
            padding: 5px;
        }

        .purpose-div {
            padding: 10px;
            border: 1px solid black;
            min-height: 50px;
            border-top: none;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .signatories-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .signatories-table td {
            border: 1px solid black;
            padding: 5px;
        }
    </style>
</head>

<body>
    <div class="header clearfix">
        <h2 class="header-h2">PURCHASE REQUEST</h2>
        <div class="header-row">
            <div class="header-col">LGU: <span class="header-text">Provincial Government of Catanduanes</span>
            </div>
            <div class="header-col">Fund: <span class="header-text"></span>
            </div>
        </div>
    </div>
    <div class="details-div clearfix">
        <div class="details-col details-col-1">
            <table>
                <tr>
                    <td class="pt-5">Department</td>
                    <td class="pt-5">:</td>
                    <td class="pt-5 underline">Provincial Health Office</td>
                </tr>
                <tr>
                    <td class="pt-5">Section</td>
                    <td class="pt-5">:</td>
                    <td class="pt-5 underline"></td>
                </tr>
            </table>
        </div>
        <div class="details-col details-col-2">
            <table>
                <tr>
                    <td class="pt-5">PR No.</td>
                    <td class="pt-5">:</td>
                    <td class="pt-5 underline"></td>
                </tr>
                <tr>
                    <td class="pt-5">SAI No.</td>
                    <td class="pt-5">:</td>
                    <td class="pt-5 underline"></td>
                </tr>
                <tr>
                    <td class="pt-5">ALOBS No.</td>
                    <td class="pt-5">:</td>
                    <td class="pt-5 underline"></td>
                </tr>
            </table>
        </div>
        <div class="details-col details-col-3">
            <table>
                <tr>
                    <td class="pt-5">Date</td>
                    <td class="pt-5">:</td>
                    <td class="pt-5 underline"></td>
                </tr>
                <tr>
                    <td class="pt-5">Date</td>
                    <td class="pt-5">:</td>
                    <td class="pt-5 underline"></td>
                </tr>
                <tr>
                    <td class="pt-5">Date</td>
                    <td class="pt-5">:</td>
                    <td class="pt-5 underline"></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="table-div">
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:50px;">Item No.</th>
                    <th>Unit</th>
                    <th style="width: 250px;">Item Description</th>
                    <th>Quantity</th>
                    <th>Unit Cost</th>
                    <th>Total Cost</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td style="text-align:center;">{{ $loop->iteration }}</td>
                        <td>{{ $item['uom'] }}</td>
                        <td>{{ $item['item_name'] }}: {{ $item['item_detail']['description'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td><span
                                class="peso">&#8369;</span>{{ number_format($item['markup_price'], $item['markup_price'] == floor($item['markup_price']) ? 0 : 2) }}
                        </td>
                        <td><span
                                class="peso">&#8369;</span>{{ number_format($item['subtotal'], $item['subtotal'] == floor($item['subtotal']) ? 0 : 2) }}
                        </td>
                    </tr>
                @endforeach
                <tr style="background-color:#f2f2f2;">
                    <td></td>
                    <td></td>
                    <td style="font-weight:bold; text-transform:uppercase;">TOTAL</td>
                    <td></td>
                    <td></td>
                    <td style="font-weight:bold;"><span class="peso">&#8369;</span>
                        {{ number_format($total, $total == floor($total) ? 0 : 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="purpose-div">
        Purpose: For Non Communicable Program Office.
    </div>

    <table class="signatories-table">
        <tr>
            <td>
                <p>Requested By:</p>
                <div style="width:100%; text-align:center;">
                    <p style="margin-top:20px; text-decoration:underline; font-weight:bold; text-transform:uppercase;">
                        HAZEL A. PALMES, M.D.</p>
                    <p style="margin-top:-10px;">Provincial Health Officer II</p>
                </div>
            </td>
            <td>
                <p>Cash Availability:</p>
                <div style="width:100%; text-align:center;">
                    <p style="margin-top:20px; text-decoration:underline; font-weight:bold; text-transform:uppercase;">
                        ERME T. TABLANTE</p>
                    <p style="margin-top:-10px;">Acting Provincial Treasurer</p>
                </div>
            </td>
            <td>
                <p>Approved by:</p>
                <div style="width:100%; text-align:center;">
                    <p style="margin-top:20px; text-decoration:underline; font-weight:bold; text-transform:uppercase;">
                        JOSEPH C. CUA
                    </p>
                    <p style="margin-top:-10px;">Provincial Governor</p>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
