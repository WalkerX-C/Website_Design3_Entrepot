/*
    MY Store - Product Detail JavaScript

    Buy Now has been removed.
    Only Add to Bag is kept.

    The selected product is saved into localStorage,
    so cart / bag page can read it later.
*/

function parsePriceNumber(priceText) {
    var match = String(priceText || "").match(/(\d+(\.\d+)?)/);
    return match ? Number(match[1]) : 0;
}

function addCurrentToBag() {
    if (!productPageData || productPageData.id === "0") {
        alert("This product cannot be added to bag.");
        return;
    }

    var bag = JSON.parse(localStorage.getItem("shop_bag")) || [];

    bag.push({
        id: "product-" + productPageData.id + "-" + Date.now(),
        productID: productPageData.id,
        name: productPageData.name,
        category: "Product",
        brand: productPageData.brand,
        model: productPageData.model,
        year: productPageData.year,
        colour: productPageData.colour,
        location: productPageData.location,
        price: parsePriceNumber(productPageData.priceText),
        priceText: productPageData.priceText,
        quantity: 1,
        img: productPageData.image
    });

    localStorage.setItem("shop_bag", JSON.stringify(bag));

    alert(productPageData.name + " added to your bag.");
}

window.onload = function () {
    if (productPageData && productPageData.id !== "0") {
        document.title = "MY Store - " + productPageData.name;
    }
};