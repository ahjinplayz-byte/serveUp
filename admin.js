
// Ensure this code runs after the DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('myTable');
    const pendingCounter = document.getElementById('pending');

    if (table && pendingCounter) {
        // Subtract 1 to exclude the <th> header row
        pendingCounter.textContent = Math.max(0, table.rows.length - 1);
    }
});

//---------------------------------------------------------------------//
//navbar------------------------------------------------------------------//
const showNavbar = document.getElementById("showNavbar");
const productNavbar = document.getElementById("productNavbar");

showNavbar.addEventListener("click", function(event) {
    event.preventDefault();
    productNavbar.classList.toggle("show");
});
//------------------------------------------------//



//-----------------------EXPENSES TABLE---------------------------//

document.addEventListener("DOMContentLoaded", function () {

    const tableBody = document.querySelector("#myTable tbody");
    const addRowButton = document.querySelector("#addRow");

    //Make sure the elements exist//
    if (!tableBody || !addRowButton) {
        console.error("Table or Add Row button was not found.");
        return;
    }


    //---------------- ADD ROW ----------------//

    addRowButton.addEventListener("click", function () {

        let rowNumber = tableBody.rows.length + 1;

        let row = tableBody.insertRow();


        //Product//
        let productCell = row.insertCell();

        productCell.contentEditable = "true";
        productCell.dataset.id = rowNumber;
        productCell.dataset.column = "product";
        productCell.textContent = "New Product";


        //Unit//
        let unitCell = row.insertCell();

        unitCell.contentEditable = "true";
        unitCell.dataset.id = rowNumber;
        unitCell.dataset.column = "unit";
        unitCell.textContent = "0";


        //Cost//
        let costCell = row.insertCell();

        costCell.contentEditable = "true";
        costCell.dataset.id = rowNumber;
        costCell.dataset.column = "cost";
        costCell.textContent = "0";


        //Total//
        let totalCell = row.insertCell();

        totalCell.textContent = "";


        //Add editing function//
        addEditListener(productCell);
        addEditListener(unitCell);
        addEditListener(costCell);


        //Recalculate total//
        calculateTotal();

    });
//--------------CLEAR TABLE----------------//
    clearTableButton.addEventListener("click", function () {

        if (confirm("Are you sure you want to clear all expenses?")) {

            tableBody.innerHTML = "";

        }

    });

    // ---------------- CALCULATE TOTAL ----------------//

    function calculateTotal() {

        let rows = tableBody.querySelectorAll("tr");

        let total = 0;


        rows.forEach(function (row) {

            let costCell = row.cells[2];

            let cost = parseFloat(
                costCell.innerText.trim()
            ) || 0;

            total += cost;

        });


        // Remove total from every row//
        rows.forEach(function (row) {

            row.cells[3].textContent = "";

        });


        // Show total ONLY on last row//
        if (rows.length > 0) {

            rows[rows.length - 1]
                .cells[3]
                .textContent = total.toFixed(2);

        }

    }


    // ---------------- EDIT CELL ----------------//

    function addEditListener(cell) {

        cell.addEventListener("blur", function () {

            let id = this.dataset.id;
            let column = this.dataset.column;
            let value = this.innerText.trim();


            // Recalculate when Cost changes//
            if (column === "cost") {

                calculateTotal();

            }


            // Send data to PHP//
            fetch("update.php", {

                method: "POST",

                headers: {
                    "Content-Type":
                        "application/x-www-form-urlencoded"
                },

                body:
                    `id=${encodeURIComponent(id)}` +
                    `&column=${encodeURIComponent(column)}` +
                    `&value=${encodeURIComponent(value)}`

            })

            .then(response => response.text())

            .then(data => {

                console.log("Server response:", data);

            })

            .catch(error => {

                console.error("Error:", error);

            });

        });

    }


    // ---------------- EXISTING CELLS ----------------//

    document
        .querySelectorAll(
            '#myTable td[contenteditable="true"]'
        )
        .forEach(function (cell) {

            addEditListener(cell);

        });

});
//-----------------------------------------------------------------//

//Print Table//
document.getElementById("printButton").addEventListener("click", function () {
    window.print();
});
//----------//
//--------Date automatic--------//
const today = new Date();

const options = {
    year: "numeric",
    month: "long",
    day: "numeric"
};

document.getElementById("currentDate").textContent =
    today.toLocaleDateString("en-US", options);
//--------------------------------//
//----------Clear Table----------//
const clearTableButton = document.getElementById("clearTable");

clearTableButton.addEventListener("click", function () {

    if (confirm("Are you sure you want to clear the table?")) {

        tableBody.innerHTML = "";

    }

});
clearTableButton.addEventListener("click", function () {

    clearTableButton.addEventListener("click", function () {

    if (confirm("Clear all expenses?")) {
        tableBody.innerHTML = "";
    }

});

});
//-------------------------------//