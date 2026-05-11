function addCurrentToBag() {
    if (!productPageData || productPageData.id === "0") {
        alert("This product cannot be added to bag.");
        return;
    }

    var form = document.createElement("form");
    form.method = "POST";
    form.action = "add_to_cart.php";
    form.style.display = "none";

    var productInput = document.createElement("input");
    productInput.type = "hidden";
    productInput.name = "product_id";
    productInput.value = productPageData.id;
    form.appendChild(productInput);

    var redirectInput = document.createElement("input");
    redirectInput.type = "hidden";
    redirectInput.name = "redirect";
    redirectInput.value = "bag.php";
    form.appendChild(redirectInput);

    document.body.appendChild(form);
    form.submit();
}

window.onload = function () {
    if (productPageData && productPageData.id !== "0") {
        document.title = "MY Store - " + productPageData.name;
    }
};
