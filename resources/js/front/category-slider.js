let categoryList, backArrow, nextArrow, currentCategory, allCategoryLinks;

const categorySlider = function () {
    const init = function () {
        categoryList = document.querySelector(".filter-list");
        backArrow = document.querySelector(".action-prev a");
        nextArrow = document.querySelector(".action-next a");
        currentCategory = categoryList.querySelector("li.active");
        allCategoryLinks = categoryList.querySelectorAll("li.category a");

        bindEvents();
        render();
        forceActivePosition();
    };

    const bindEvents = function () {
        window.addEventListener("resize", render);
        categoryList.addEventListener("scroll", render);
        backArrow.addEventListener("click", backClicked);
        nextArrow.addEventListener("click", nextClicked);
        allCategoryLinks.forEach(function (category) {
            category.addEventListener("click", categoryClicked);
        });
    };

    const render = function () {
        const edgeLeft = 0 < categoryList.scrollLeft;
        const edgeRight =
            categoryList.scrollLeft + categoryList.clientWidth >=
            categoryList.scrollWidth;

        if (edgeLeft) {
            if (!backArrow.parentElement.classList.contains("-show")) {
                backArrow.parentElement.classList.add("-show");
            } else if (backArrow.parentElement.classList.contains("-hide")) {
                backArrow.parentElement.classList.remove("-hide");
                backArrow.parentElement.classList.add("-show");
            }
        } else {
            if (!backArrow.parentElement.classList.contains("-hide")) {
                backArrow.parentElement.classList.add("-hide");
                backArrow.parentElement.classList.remove("-show");
            }
        }

        if (edgeRight) {
            if (!nextArrow.parentElement.classList.contains("-hide")) {
                nextArrow.parentElement.classList.add("-hide");
                nextArrow.parentElement.classList.remove("-show");
            }
        } else {
            if (nextArrow.parentElement.classList.contains("-hide")) {
                nextArrow.parentElement.classList.toggle("-hide");
                nextArrow.parentElement.classList.add("-show");
            }
            if (!nextArrow.parentElement.classList.contains("-show")) {
                nextArrow.parentElement.classList.add("-show");
            }
        }
    };

    const nextClicked = function () {
        categoryList.scrollLeft += categoryList.clientWidth;
    };

    const backClicked = function () {
        categoryList.scrollLeft -= categoryList.clientWidth;
    };

    const categoryClicked = function ($event) {
        categoryList.querySelector("li.active").classList.remove("active");
        $event.currentTarget.parentElement.classList.add("active");

        const categoryCurrentPosition =
            $event.currentTarget.offsetLeft + $event.currentTarget.clientWidth;
        const edgeLimitRight =
            categoryList.clientWidth +
            categoryList.scrollLeft -
            nextArrow.clientWidth;
        const offsetToLeft =
            $event.currentTarget.offsetLeft - backArrow.clientWidth;
        const offsetToRight =
            $event.currentTarget.offsetLeft - nextArrow.clientWidth;

        if (offsetToLeft < categoryList.scrollLeft && offsetToRight) {
            categoryList.scrollLeft = offsetToRight;
        }
        if (edgeLimitRight < categoryCurrentPosition) {
            categoryList.scrollLeft =
                categoryList.scrollLeft +
                categoryCurrentPosition -
                edgeLimitRight;
        }
    };

    const forceActivePosition = function () {
        if (currentCategory) {
            const currentCatWidth =
                currentCategory.offsetLeft + currentCategory.clientWidth;
            const restCurrentCatWidth =
                categoryList.clientWidth - nextArrow.clientWidth;
            const currentCatNearToEnd =
                currentCategory.offsetLeft - nextArrow.clientWidth;

            if (
                currentCategory &&
                (currentCatWidth < restCurrentCatWidth || currentCatNearToEnd)
            ) {
                categoryList.scrollLeft = currentCatNearToEnd;
            }
        }
    };

    return {
        init,
    };
};

export { categorySlider };
