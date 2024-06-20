document.addEventListener("DOMContentLoaded", function () {
    // Load the countries and categories data
    var CatSelected = false;
    loadJSON(agerixcarte_vars.plugin_url + "/assets/js/countries-data.json")
        .then((countriesData) => {
            return loadJSON(
                agerixcarte_vars.plugin_url + "/assets/js/categories-data.json"
            ).then((categoriesData) => {
                initializeSVG(countriesData, categoriesData);
                populateCategorySelect(categoriesData, countriesData);
            });
        })
        .catch((error) => {
            console.error("Error loading JSON data:", error);
        });
    // Function to load JSON data
    function loadJSON(url) {
        return fetch(url).then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok " + response.statusText);
            }
            return response.json();
        });
    }

    // Function to initialize the SVG and add event listeners to paths
    function initializeSVG(countriesData, categoriesData) {
        var svg = document.getElementById("worldmap-svg");
        if (svg) {
            var countriesGroup = svg.getElementById("countries");
            if (countriesGroup) {
                let paths = countriesGroup.querySelectorAll("path");
                
                paths.forEach(function (path) {
                    var countryId = path.getAttribute("id");
                    var countryData = countriesData[countryId];

                    //console.log(countriesData);
                    
                    if (countryData) {
                        // Set custom attributes for each path element
                        path.setAttribute("data-json-id", countryData.countryJsonId);
                        path.setAttribute(
                            "data-json-name",
                            countryData["country-json-name"]
                        );
                        path.setAttribute(
                            "data-json-continent",
                            countryData["country-json-continent"]
                        );

                        // Add event listener for mouseover event
                        path.addEventListener("mouseover", function () {
                            var category = getCategoryForCountry(countryId, categoriesData);
                            if (category && !CatSelected) {
                                //console.log(category.countries);
                                path.style.fill = category.color;
                            }
                        });

                        // Add event listener for mouseout event
                        path.addEventListener("mouseout", function () {
                            if (!CatSelected){
                            path.style.fill = "";   
                            }               
                        });
                    }
                });
            } else {
                console.error('Element with ID "countries" not found in the SVG.');
            }
        } else {
            console.error('SVG with ID "worldmap-svg" not found.');
        }
    }

    // Function to populate the select element for categories
    function populateCategorySelect(categoriesData, countriesData) {
        var select = document.getElementById("category-select");
        if (select) {
            for (var categoryKey in categoriesData) {
                var option = document.createElement("option");
                option.value = categoryKey;
                option.text = categoryKey;
                select.appendChild(option);
            }
    
            // Add event listener for change event
            select.addEventListener("change", function () {
                var selectedCategory = categoriesData[this.value];
                var listcountry = document.getElementById("list-country");
                listcountry.innerHTML = ""; // Reset the list of countries for the selected category
    
                if (selectedCategory) {
                    var countriesList = selectedCategory.countries;
                    countriesList.forEach(function(country) {
                        if (country) {
                            var countryID = countriesData[country];
                            if (countryID) {
                                var listcountryLi = document.createElement("li");
                                listcountryLi.innerHTML = countryID.countryJsonName;
                                listcountry.appendChild(listcountryLi);
                            }
                        }
                    });
    
                    CatSelected = true;
                    colorCountriesByCategory(selectedCategory); // Color countries for the selected category
                } else if (this.value === "reset") {
                    resetCountryColors();
                    listcountry.innerHTML = "";
                    CatSelected = false;
                }
            });
        } else {
            console.error('Select element with ID "category-select" not found.');
        }
    }
    // Function to reset the colors of all countries
    function resetCountryColors() 
    {
        var svg = document.getElementById("worldmap-svg");
        if (svg) {
            var countriesGroup = svg.getElementById("countries");
            if (countriesGroup) {
                var paths = countriesGroup.querySelectorAll("path");

                paths.forEach(function (path) {
                    path.style.fill = "";
                });
            }
        }
    }

    // Function to color countries by the selected category
    function colorCountriesByCategory(selectedCategory) {
        var svg = document.getElementById("worldmap-svg");
        if (svg) {
            var countriesGroup = svg.getElementById("countries");
            if (countriesGroup) {
                var paths = countriesGroup.querySelectorAll("path");

                paths.forEach(function (path) {
                    var countryId = path.getAttribute("id");
                    if (selectedCategory.countries.includes(countryId)) {
                        path.style.fill = selectedCategory.color;
                    } else {
                        path.style.fill = "";
                    }
                });
            }
        }
    }

    // Function to get the category for a country
    function getCategoryForCountry(countryId, categoriesData) {
        for (var categoryKey in categoriesData) {
            var category = categoriesData[categoryKey];
            if (category.countries && category.countries.includes(countryId)) {
                return category;
            }
        }
        return null;
    }    
    function getListCountries(categoryName, categoriesData) {
        // Vérifiez d'abord si le categoryName est défini et s'il existe dans categoriesData
        if (!categoryName || !categoriesData[categoryName]) {
            console.error('Category not found or categoryName is not defined');
            return [];
        }
    
        // Récupérez les pays pour la catégorie sélectionnée
        var countries = categoriesData[categoryName].countries;
    
        // Vérifiez si countries est défini et est un tableau
        if (!Array.isArray(countries)) {
            console.error('Invalid countries data for category:', categoryName);
            return [];
        }
    
        // Retournez la liste des pays
        return countries;
    }
});
