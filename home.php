<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Panel - Restaurant Menu (eray edit)</title>
  <style>
    body {
      font-family: sans-serif;
      padding: 30px;
      background: #f5f5f5;
      color: #333;
      max-width: 900px;
      margin: auto;
    }
    h1 {
      color: #c94f4f;
      text-align: center;
    }
    section {
      margin-bottom: 40px;
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    label {
      display: block;
      margin-top: 10px;
      font-weight: 600;
    }
    input, textarea, select {
      width: 100%;
      padding: 8px;
      margin-top: 4px;
      border-radius: 8px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }
    button {
      margin-top: 20px;
      background-color: #c94f4f;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }
    button:hover {
      background-color: #a03a3a;
    }
    .inline-btn {
      display: inline-block;
      margin-left: 10px;
      background: #777;
      padding: 5px 10px;
      border-radius: 6px;
      font-size: 0.9em;
    }
    .inline-btn:hover {
      background: #555;
    }
    .category-list, .food-list {
      margin-top: 10px;
    }
    .category-item, .food-item {
      padding: 6px 8px;
      background: #eee;
      margin-bottom: 6px;
      border-radius: 6px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .food-item {
      margin-left: 20px;
    }
    .category-item-clickable {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .category-item-clickable:hover {
        background-color: #e0e0e0;
    }
    .lang-toggle-container {
        position: absolute;
        top: 20px;
        right: 30px;
    }
    .lang-toggle-button {
        background-color: #007bff;
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }
    .lang-toggle-button:hover {
        background-color: #0056b3;
    }
  </style>
</head>
<body>

  <div class="lang-toggle-container">
    <button class="lang-toggle-button" onclick="toggleLanguage()"></button>
  </div>

  <h1>🍴 <span data-lang-key="adminPanelTitle">Admin Panel</span></h1>

  <section>
    <h2 data-lang-key="addNewCategoryTitle">Add New Category</h2>
    <label data-lang-key="categoryNameLabelEN">Category Name (English)</label>
    <input id="newCategoryNameEn" placeholder="e.g. Appetizers" />
    <label data-lang-key="categoryNameLabelTR">Category Name (Turkish)</label>
    <input id="newCategoryNameTr" placeholder="örn. Başlangıçlar" />

    <label data-lang-key="categoryBgImageLabel">Category Background Image</label>
    <input id="newCategoryBg" type="file" accept="image/*" />

    <button onclick="addCategory()" data-lang-key="addCategoryButton">Add Category</button>
  </section>

  <section>
    <h2 data-lang-key="selectCategoryToEditTitle">Select a Category to Edit</h2>
    <div id="categorySelectionList" class="category-list">
      </div>
  </section>

  <section id="editCategorySection" style="display:none;">
    <h2 data-lang-key="editCategoryTitle">Edit Category: <span id="currentEditingCategoryName"></span></h2>
    <label data-lang-key="categoryNameLabelEN">Category Name (English)</label>
    <input id="editCategoryNameEn" />
    <label data-lang-key="categoryNameLabelTR">Category Name (Turkish)</label>
    <input id="editCategoryNameTr" />

    <label data-lang-key="categoryBgImageLabel">Category Background Image (upload to replace)</label>
    <input id="editCategoryBg" type="file" accept="image/*" />

    <button onclick="saveCategoryEdit()" data-lang-key="saveCategoryButton">Save Category</button>
    <button onclick="deleteCategoryWrapper()" style="background:#d9534f;" data-lang-key="deleteCategoryButton">Delete Category</button>
    <button onclick="cancelCategoryEdit()" style="background:#888;" data-lang-key="cancelButton">Cancel</button>

    <hr>
    <h3 data-lang-key="foodsInThisCategoryTitle">Foods in this category</h3>
    <div id="foodList" class="food-list"></div>

    <hr>
    <h3 data-lang-key="addNewFoodTitle">Add New Food to <span id="currentFoodCategoryName"></span></h3>
    <label data-lang-key="foodNameLabelEN">Food Name (English)</label>
    <input id="newFoodNameEn" placeholder="e.g. Bruschetta" />
    <label data-lang-key="foodNameLabelTR">Food Name (Turkish)</label>
    <input id="newFoodNameTr" placeholder="örn. Bruschetta" />

    <label data-lang-key="descriptionLabelEN">Description (English)</label>
    <textarea id="newFoodDescEn" rows="2"></textarea>
    <label data-lang-key="descriptionLabelTR">Description (Turkish)</label>
    <textarea id="newFoodDescTr" rows="2"></textarea>

    <label data-lang-key="priceLabel">Price</label>
    <input id="newFoodPrice" type="number" step="0.01" />

    <label data-lang-key="foodPhotoLabel">Food Photo</label>
    <input id="newFoodPhoto" type="file" accept="image/*" />
    
    <button onclick="addFood()" data-lang-key="addFoodButton">Add Food</button>
  </section>

  <section id="editFoodSection" style="display:none;">
    <h2 data-lang-key="editFoodTitle">Edit Food</h2>
    <label data-lang-key="foodNameLabelEN">Food Name (English)</label>
    <input id="editFoodNameEn" />
    <label data-lang-key="foodNameLabelTR">Food Name (Turkish)</label>
    <input id="editFoodNameTr" />

    <label data-lang-key="descriptionLabelEN">Description (English)</label>
    <textarea id="editFoodDescEn" rows="2"></textarea>
    <label data-lang-key="descriptionLabelTR">Description (Turkish)</label>
    <textarea id="editFoodDescTr" rows="2"></textarea>

    <label data-lang-key="priceLabel">Price</label>
    <input id="editFoodPrice" type="number" step="0.01" />

    <label data-lang-key="foodPhotoLabel">Food Photo (upload to replace)</label>
    <input id="editFoodPhoto" type="file" accept="image/*" />

    <button onclick="saveFoodEdit()" data-lang-key="saveFoodButton">Save Food</button>
    <button onclick="cancelFoodEdit()" style="background:#888;" data-lang-key="cancelButton">Cancel</button>
  </section>

<script>
  let menu = {};
  let currentCategoryKey = null; // Stores the English name of the currently selected category
  let currentFoodIndex = null;
  let currentUILanguage = 'en'; // Default language

  // Dictionary for UI translations
  const translations = {
      en: {
          adminPanelTitle: "Admin Panel",
          addNewCategoryTitle: "Add New Category",
          categoryNameLabelEN: "Category Name (English)",
          categoryNameLabelTR: "Category Name (Turkish)",
          categoryBgImageLabel: "Category Background Image",
          addCategoryButton: "Add Category",
          selectCategoryToEditTitle: "Select a Category to Edit",
          editCategoryTitle: "Edit Category: ",
          saveCategoryButton: "Save Category",
          deleteCategoryButton: "Delete Category",
          cancelButton: "Cancel",
          foodsInThisCategoryTitle: "Foods in this category",
          addNewFoodTitle: "Add New Food to ",
          foodNameLabelEN: "Food Name (English)",
          foodNameLabelTR: "Food Name (Turkish)",
          descriptionLabelEN: "Description (English)",
          descriptionLabelTR: "Description (Turkish)",
          priceLabel: "Price",
          foodPhotoLabel: "Food Photo",
          addFoodButton: "Add Food",
          editFoodTitle: "Edit Food",
          saveFoodButton: "Save Food",
          
          // Alerts and confirmations
          alertEnterCategoryName: "Please enter category names in both English and Turkish.",
          alertCategoryExists: "A category with this English or Turkish name already exists!",
          alertSelectBgImage: "Please select a background image for the category.",
          alertCategoryAddedPhotoInstructions: "Category added! Manually move/rename the background image to: images/categories/",
          confirmDeleteCategory: "Are you sure you want to delete the category \"{0}\" and all its foods?",
          alertCategoryDeleted: "Category \"{0}\" deleted.",
          alertCategoryNameEmpty: "Category names (English and Turkish) cannot be empty.",
          alertCategoryNameExists: "A category with this English or Turkish name already exists!",
          alertNewBgImageInstructions: "Manually move/rename the new background image to: ",
          alertCategorySaved: "Category saved!",
          alertFillAllFoodFields: "Please fill in all text fields (English and Turkish) and enter a valid price.",
          alertSelectFoodPhoto: "Please select a food photo.",
          alertFoodNameExistsInCategory: "A food with the name \"{0}\" already exists in the \"{1}\" category!",
          alertFoodAddedPhotoInstructions: "Food added! Manually move/rename the photo to: images/foods/",
          confirmDeleteFood: "Are you sure you want to delete \"{0}\"?",
          alertFoodDeleted: "Food deleted.",
          alertFillAllEditFoodFields: "Please fill in all fields correctly (English, Turkish, and Price).",
          alertFoodNameExistsInEdit: "A food with the name \"{0}\" already exists in this category!",
          alertNewFoodPhotoInstructions: "Manually move/rename the new photo to: ",
          alertFoodSaved: "Food saved!",
          foodNotFoundInCategory: "No food found in this category.",
          selectCategoryMessage: "Click on a category above to edit it."

      },
      tr: {
          adminPanelTitle: "Yönetim Paneli",
          addNewCategoryTitle: "Yeni Kategori Ekle",
          categoryNameLabelEN: "Kategori Adı (İngilizce)",
          categoryNameLabelTR: "Kategori Adı (Türkçe)",
          categoryBgImageLabel: "Kategori Arka Plan Resmi",
          addCategoryButton: "Kategori Ekle",
          selectCategoryToEditTitle: "Düzenlemek İçin Bir Kategori Seçin",
          editCategoryTitle: "Kategoriyi Düzenle: ",
          saveCategoryButton: "Kategoriyi Kaydet",
          deleteCategoryButton: "Kategoriyi Sil",
          cancelButton: "İptal",
          foodsInThisCategoryTitle: "Bu kategorideki yemekler",
          addNewFoodTitle: "Yeni Yemek Ekle: ",
          foodNameLabelEN: "Yemek Adı (İngilizce)",
          foodNameLabelTR: "Yemek Adı (Türkçe)",
          descriptionLabelEN: "Açıklama (İngilizce)",
          descriptionLabelTR: "Açıklama (Türkçe)",
          priceLabel: "Fiyat",
          foodPhotoLabel: "Yemek Fotoğrafı",
          addFoodButton: "Yemek Ekle",
          editFoodTitle: "Yemeği Düzenle",
          saveFoodButton: "Yemeği Kaydet",

          // Alerts and confirmations
          alertEnterCategoryName: "Lütfen hem İngilizce hem de Türkçe kategori adlarını girin.",
          alertCategoryExists: "Bu İngilizce veya Türkçe isimde bir kategori zaten mevcut!",
          alertSelectBgImage: "Lütfen kategori için bir arka plan resmi seçin.",
          alertCategoryAddedPhotoInstructions: "Kategori eklendi! Arka plan resmini elle şu konuma taşıyın/yeniden adlandırın: images/categories/",
          confirmDeleteCategory: "\"{0}\" kategorisini ve tüm yemeklerini silmek istediğinizden emin misiniz?",
          alertCategoryDeleted: "\"{0}\" kategorisi silindi.",
          alertCategoryNameEmpty: "Kategori adları (İngilizce ve Türkçe) boş olamaz.",
          alertCategoryNameExists: "Bu İngilizce veya Türkçe isimde bir kategori zaten mevcut!",
          alertNewBgImageInstructions: "Yeni arka plan resmini elle şu konuma taşıyın/yeniden adlandırın: ",
          alertCategorySaved: "Kategori kaydedildi!",
          alertFillAllFoodFields: "Lütfen tüm metin alanlarını (İngilizce ve Türkçe) doldurun ve geçerli bir fiyat girin.",
          alertSelectFoodPhoto: "Lütfen bir yemek fotoğrafı seçin.",
          alertFoodNameExistsInCategory: "\"{0}\" adlı yemek \"{1}\" kategorisinde zaten mevcut!",
          alertFoodAddedPhotoInstructions: "Yemek eklendi! Fotoğrafı elle şu konuma taşıyın/yeniden adlandırın: images/foods/",
          confirmDeleteFood: "\"{0}\" silmek istediğinizden emin misiniz?",
          alertFoodDeleted: "Yemek silindi.",
          alertFillAllEditFoodFields: "Lütfen tüm alanları doğru şekilde doldurun (İngilizce, Türkçe ve Fiyat).",
          alertFoodNameExistsInEdit: "Bu kategoride \"{0}\" adlı bir yemek zaten mevcut!",
          alertNewFoodPhotoInstructions: "Yeni fotoğrafı elle şu konuma taşıyın/yeniden adlandırın: ",
          alertFoodSaved: "Yemek kaydedildi!",
          foodNotFoundInCategory: "Bu kategoride yemek bulunamadı.",
          selectCategoryMessage: "Düzenlemek için yukarıdaki bir kategoriye tıklayın."
      }
  };

  // Function to get translated text
  function getTranslation(key, ...args) {
      let text = translations[currentUILanguage][key] || key; // Fallback to key if translation not found
      args.forEach((arg, i) => {
          text = text.replace(`{${i}}`, arg);
      });
      return text;
  }

  // Function to update all UI text based on currentUILanguage
  function updateUIText() {
      document.querySelectorAll('[data-lang-key]').forEach(element => {
          const key = element.getAttribute('data-lang-key');
          element.textContent = getTranslation(key);
      });
      document.querySelector('.lang-toggle-button').textContent = currentUILanguage === 'en' ? 'Türkçe' : 'English';

      // Update dynamic category name in edit section
      if (currentCategoryKey && document.getElementById('editCategorySection').style.display === 'block') {
          const categoryNameDisplay = document.getElementById('currentEditingCategoryName');
          categoryNameDisplay.textContent = menu[currentCategoryKey].title[currentUILanguage] || currentCategoryKey;
          const foodCategoryNameDisplay = document.getElementById('currentFoodCategoryName');
          foodCategoryNameDisplay.textContent = menu[currentCategoryKey].title[currentUILanguage] || currentCategoryKey;
      } else {
        // If no category is selected or section is hidden, clear these
        document.getElementById('currentEditingCategoryName').textContent = '';
        document.getElementById('currentFoodCategoryName').textContent = '';
      }
  }

  function toggleLanguage() {
      currentUILanguage = currentUILanguage === 'en' ? 'tr' : 'en';
      updateUIText();
      renderCategorySelectionList(); // Re-render category selection list
      // If a category is being edited, re-render its foods to update display language
      if (currentCategoryKey) {
          renderFoodList(currentCategoryKey);
      }
      // If a food is being edited, re-populate input fields
      if (document.getElementById('editFoodSection').style.display === 'block' && currentFoodIndex !== null) {
          openFoodEdit(currentFoodIndex);
      }
  }


  // Menüyü Python sunucusundan yükle
  window.onload = async () => {
    try {
      const response = await fetch('http://localhost:5000/api/menu');
      if (!response.ok) {
        if (response.status === 200) {
          menu = {};
          console.log("Menü verisi sunucuda bulunamadı, boş menü başlatılıyor.");
        } else {
          throw new Error('Menü verisi sunucudan yüklenemedi: ' + response.statusText);
        }
      } else {
        menu = await response.json();
      }
      renderCategorySelectionList();
    } catch (e) {
      console.error('Menü verisi yüklenirken hata oluştu:', e);
      menu = {}; // Hata durumunda boş menü ile başla
      renderCategorySelectionList();
    }
    updateUIText(); // Initial UI text update
  };

  // Menüyü Python sunucusuna kaydet
  async function saveMenuToServer() {
    try {
      const response = await fetch('http://localhost:5000/api/menu', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(menu),
      });
      if (!response.ok) {
        const errorData = await response.json();
        throw new Error('Menü kaydedilirken hata: ' + errorData.error);
      }
      const data = await response.json();
      console.log('Menü sunucuya kaydedildi:', data.message);
    } catch (error) {
      console.error('Menü kaydedilemedi:', error);
      alert('Menü kaydedilirken bir hata oluştu: ' + error.message);
    }
  }

  // Renders the list of categories for selection
  function renderCategorySelectionList() {
    const container = document.getElementById('categorySelectionList');
    container.innerHTML = '';

    if (Object.keys(menu).length === 0) {
        container.textContent = getTranslation('selectCategoryMessage');
        return;
    }

    Object.keys(menu).forEach(catKey => {
      const div = document.createElement('div');
      div.className = 'category-item category-item-clickable';
      div.textContent = menu[catKey].title[currentUILanguage] || catKey;
      div.onclick = () => openCategoryEdit(catKey);
      container.appendChild(div);
    });
  }

  function openCategoryEdit(catKey) {
    currentCategoryKey = catKey;
    document.getElementById('editCategorySection').style.display = 'block';
    document.getElementById('editFoodSection').style.display = 'none';

    // Update the dynamic category names in the heading and food addition section
    document.getElementById('currentEditingCategoryName').textContent = menu[catKey].title[currentUILanguage] || catKey;
    document.getElementById('currentFoodCategoryName').textContent = menu[catKey].title[currentUILanguage] || catKey;


    document.getElementById('editCategoryNameEn').value = menu[catKey].title.en || '';
    document.getElementById('editCategoryNameTr').value = menu[catKey].title.tr || '';
    document.getElementById('editCategoryBg').value = ''; // Clear file input for security/clarity

    renderFoodList(catKey);
  }

  function renderFoodList(catKey) {
    const container = document.getElementById('foodList');
    container.innerHTML = '';

    // Kategori yoksa veya foods dizisi boşsa
    if (!menu[catKey] || !menu[catKey].foods || menu[catKey].foods.length === 0) {
        container.textContent = getTranslation('foodNotFoundInCategory');
        return;
    }

    menu[catKey].foods.forEach((food, i) => {
      const div = document.createElement('div');
      div.className = 'food-item';
      div.textContent = food.name[currentUILanguage] || food.name.en || ''; // Display translated name

      const editBtn = document.createElement('button');
      editBtn.textContent = getTranslation('saveFoodButton').split(' ')[0]; // "Save" part for "Edit"
      editBtn.className = 'inline-btn';
      editBtn.onclick = () => openFoodEdit(i);

      const deleteBtn = document.createElement('button');
      deleteBtn.textContent = getTranslation('cancelButton'); // "Cancel" for "Delete"
      deleteBtn.className = 'inline-btn';
      deleteBtn.style.backgroundColor = '#d9534f';
      deleteBtn.onclick = (event) => {
        event.stopPropagation();
        deleteFood(i);
      };

      div.appendChild(editBtn);
      div.appendChild(deleteBtn);
      container.appendChild(div);
    });
  }

  function addCategory() {
    const nameEn = document.getElementById('newCategoryNameEn').value.trim();
    const nameTr = document.getElementById('newCategoryNameTr').value.trim();
    const file = document.getElementById('newCategoryBg').files[0];

    if (!nameEn || !nameTr) return alert(getTranslation('alertEnterCategoryName'));
    
    // Check if any category already uses this English or Turkish name
    const englishNameExists = Object.keys(menu).some(key => key === nameEn);
    const turkishNameExists = Object.keys(menu).some(key => menu[key].title && menu[key].title.tr === nameTr);
    if (englishNameExists || turkishNameExists) {
        return alert(getTranslation('alertCategoryExists'));
    }

    if (!file) return alert(getTranslation('alertSelectBgImage'));

    const imageName = nameEn.replace(/\s+/g, '_') + '.jpg'; // Use English name for image path
    menu[nameEn] = { // Use English name as the primary key for the category
      title: { en: nameEn, tr: nameTr },
      bgImage: 'images/categories/' + imageName,
      foods: []
    };

    saveMenuToServer();
    renderCategorySelectionList(); // Re-render selection list
    alert(getTranslation('alertCategoryAddedPhotoInstructions') + imageName);

    document.getElementById('newCategoryNameEn').value = '';
    document.getElementById('newCategoryNameTr').value = '';
    document.getElementById('newCategoryBg').value = '';
  }

  // Wrapper for deleteCategory to tie it to currentCategoryKey
  function deleteCategoryWrapper() {
      if (currentCategoryKey) {
          deleteCategory(currentCategoryKey);
      }
  }

  function deleteCategory(catToDelete) {
    if (confirm(getTranslation('confirmDeleteCategory', menu[catToDelete].title[currentUILanguage] || catToDelete))) {
      delete menu[catToDelete];
      saveMenuToServer();
      renderCategorySelectionList(); // Re-render selection list
      cancelCategoryEdit(); // Hide the edit section
      alert(getTranslation('alertCategoryDeleted', menu[catToDelete]?.title?.[currentUILanguage] || catToDelete));
    }
  }

  function saveCategoryEdit() {
    const newNameEn = document.getElementById('editCategoryNameEn').value.trim();
    const newNameTr = document.getElementById('editCategoryNameTr').value.trim();
    const file = document.getElementById('editCategoryBg').files[0];

    if (!newNameEn || !newNameTr) return alert(getTranslation('alertCategoryNameEmpty'));

    // Check if new name (English or Turkish) conflicts with existing category names, excluding the current one
    const englishNameExists = Object.keys(menu).some(key => key !== currentCategoryKey && key === newNameEn);
    const turkishNameExists = Object.keys(menu).some(key => key !== currentCategoryKey && menu[key].title && menu[key].title.tr === newNameTr);

    if (englishNameExists || turkishNameExists) {
        return alert(getTranslation('alertCategoryNameExists'));
    }
    
    let newBgImage = menu[currentCategoryKey].bgImage;
    if (file) {
      const imageName = newNameEn.replace(/\s+/g, '_') + '.jpg'; // Use English name for image path
      newBgImage = 'images/categories/' + imageName;
      alert(getTranslation('alertNewBgImageInstructions') + newBgImage);
    }

    // If English name changed, update the primary key in the menu object
    if (newNameEn !== currentCategoryKey) {
        const categoryData = menu[currentCategoryKey];
        delete menu[currentCategoryKey];
        menu[newNameEn] = categoryData;
        currentCategoryKey = newNameEn; // Update currentCategoryKey to the new English name
    }

    // Update title and background image for the (potentially new) currentCategoryKey
    menu[currentCategoryKey].title = { en: newNameEn, tr: newNameTr };
    menu[currentCategoryKey].bgImage = newBgImage;

    saveMenuToServer();
    renderCategorySelectionList(); // Re-render selection list
    openCategoryEdit(currentCategoryKey); // Re-open the edited category to show updated name in heading
    alert(getTranslation('alertCategorySaved'));
  }

  function cancelCategoryEdit() {
    currentCategoryKey = null;
    document.getElementById('editCategorySection').style.display = 'none';
    document.getElementById('editFoodSection').style.display = 'none'; // Ensure food edit is also hidden
  }

  function addFood() {
    if (!currentCategoryKey) {
        alert("Please select a category first."); // Should not happen with UI flow, but as a safeguard
        return;
    }
    const categoryKey = currentCategoryKey; // Use the currently selected category

    const nameEn = document.getElementById('newFoodNameEn').value.trim();
    const nameTr = document.getElementById('newFoodNameTr').value.trim();
    const descEn = document.getElementById('newFoodDescEn').value.trim();
    const descTr = document.getElementById('newFoodDescTr').value.trim();
    const price = parseFloat(document.getElementById('newFoodPrice').value);
    const photoFile = document.getElementById('newFoodPhoto').files[0];

    if (!nameEn || !nameTr || !descEn || !descTr || isNaN(price)) {
      return alert(getTranslation('alertFillAllFoodFields'));
    }
    if (!photoFile) return alert(getTranslation('alertSelectFoodPhoto'));

    const photoName = nameEn.replace(/\s+/g, '_') + '.jpg';

    // Check for existing food with the same English name within the selected category
    if (menu[categoryKey].foods.some(food => food.name.en === nameEn)) {
        return alert(getTranslation('alertFoodNameExistsInCategory', nameEn, menu[categoryKey].title[currentUILanguage] || categoryKey));
    }

    menu[categoryKey].foods.push({
      name: { en: nameEn, tr: nameTr },
      description: { en: descEn, tr: descTr },
      price: price,
      photo: 'images/foods/' + photoName
    });

    saveMenuToServer();
    renderFoodList(categoryKey);
    alert(getTranslation('alertFoodAddedPhotoInstructions') + photoName);

    // Inputları temizle
    document.getElementById('newFoodNameEn').value = '';
    document.getElementById('newFoodNameTr').value = '';
    document.getElementById('newFoodDescEn').value = '';
    document.getElementById('newFoodDescTr').value = '';
    document.getElementById('newFoodPrice').value = '';
    document.getElementById('newFoodPhoto').value = '';
  }

  function deleteFood(foodIndex) {
    if (!currentCategoryKey) return; // UI doğruysa bu durum oluşmamalı
    const foodName = menu[currentCategoryKey].foods[foodIndex].name[currentUILanguage] || menu[currentCategoryKey].foods[foodIndex].name.en;
    if (confirm(getTranslation('confirmDeleteFood', foodName))) {
        menu[currentCategoryKey].foods.splice(foodIndex, 1);
        saveMenuToServer();
        renderFoodList(currentCategoryKey);
        alert(getTranslation('alertFoodDeleted'));
    }
  }

  function openFoodEdit(foodIndex) {
    currentFoodIndex = foodIndex;
    document.getElementById('editCategorySection').style.display = 'none'; // Hide category edit
    document.getElementById('editFoodSection').style.display = 'block'; // Show food edit

    const food = menu[currentCategoryKey].foods[foodIndex];
    document.getElementById('editFoodNameEn').value = food.name.en || '';
    document.getElementById('editFoodNameTr').value = food.name.tr || '';
    document.getElementById('editFoodDescEn').value = food.description.en || '';
    document.getElementById('editFoodDescTr').value = food.description.tr || '';
    document.getElementById('editFoodPrice').value = food.price;
    document.getElementById('editFoodPhoto').value = ''; // Dosya inputunu temizle
  }

  function saveFoodEdit() {
    const newNameEn = document.getElementById('editFoodNameEn').value.trim();
    const newNameTr = document.getElementById('editFoodNameTr').value.trim();
    const newDescEn = document.getElementById('editFoodDescEn').value.trim();
    const newDescTr = document.getElementById('editFoodDescTr').value.trim();
    const newPrice = parseFloat(document.getElementById('editFoodPrice').value);
    const photoFile = document.getElementById('editFoodPhoto').files[0];

    if (!newNameEn || !newNameTr || !newDescEn || !newDescTr || isNaN(newPrice)) {
      return alert(getTranslation('alertFillAllEditFoodFields'));
    }

    // Check for existing food with the same English name in the current category, excluding the one being edited
    if (menu[currentCategoryKey].foods.some((food, index) => index !== currentFoodIndex && food.name.en === newNameEn)) {
        return alert(getTranslation('alertFoodNameExistsInEdit', newNameEn));
    }

    let newPhoto = menu[currentCategoryKey].foods[currentFoodIndex].photo;
    if (photoFile) {
      const photoName = newNameEn.replace(/\s+/g, '_') + '.jpg';
      newPhoto = 'images/foods/' + photoName;
      alert(getTranslation('alertNewFoodPhotoInstructions') + newPhoto);
    }

    const food = menu[currentCategoryKey].foods[currentFoodIndex];
    food.name = { en: newNameEn, tr: newNameTr };
    food.description = { en: newDescEn, tr: newDescTr };
    food.price = newPrice;
    food.photo = newPhoto;

    saveMenuToServer();
    document.getElementById('editFoodSection').style.display = 'none';
    document.getElementById('editCategorySection').style.display = 'block'; // Show category edit after food is saved

    renderFoodList(currentCategoryKey);
    alert(getTranslation('alertFoodSaved'));
  }

  function cancelFoodEdit() {
    currentFoodIndex = null;
    document.getElementById('editFoodSection').style.display = 'none';
    document.getElementById('editCategorySection').style.display = 'block'; // Go back to category edit view
  }
</script>

</body>
</html>



