const categoryDropdown = document.getElementById("category");
        const skillsListContainer = document.getElementById("skills-list");

        // Fetch categories
        fetch("api.php?action=getCategories")
            .then(response => response.json())
            .then(categories => {
                categories.forEach(category => {
                    const option = document.createElement("option");
                    option.value = category.id;
                    option.textContent = category.name;
                    categoryDropdown.appendChild(option);
                });
            });

        // Fetch skills when a category is selected
        categoryDropdown.addEventListener("change", (e) => {
            const categoryId = e.target.value;
            if (!categoryId) return;

            fetch(`api.php?action=getSkills&category_id=${categoryId}`)
                .then(response => response.json())
                .then(skills => {
                    skillsListContainer.innerHTML = ""; // Clear previous skills
                    skills.forEach(skill => {
                        const li = document.createElement("li");
                        const checkbox = document.createElement("input");
                        checkbox.type = "checkbox";
                        checkbox.value = skill.id;
                        li.appendChild(checkbox);
                        li.appendChild(document.createTextNode(` ${skill.name}`));
                        skillsListContainer.appendChild(li);
                    });
                });
        });