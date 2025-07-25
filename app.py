# app.py
from flask import Flask, request, jsonify, send_from_directory
from flask_cors import CORS
import json
import os

app = Flask(__name__)
CORS(app) # CORS'u tüm rotalar için etkinleştir

MENU_FILE = 'public/data/menu-data.json' # Menü verilerinin saklanacağı dosya

# Eğer menu-data.json dosyası yoksa, boş bir JSON objesi ile oluştur
if not os.path.exists(MENU_FILE):
    with open(MENU_FILE, 'w', encoding='utf-8') as f:
        json.dump({}, f, ensure_ascii=False, indent=2)

@app.route('/')
def index():
    return send_from_directory('.', 'admin-panel.html')

# Menü verilerini okuma (GET)
@app.route('/api/menu', methods=['GET'])
def get_menu():
    try:
        with open(MENU_FILE, 'r', encoding='utf-8') as f:
            menu_data = json.load(f)
        return jsonify(menu_data)
    except FileNotFoundError:
        return jsonify({}), 200 # Dosya yoksa boş menü döndür
    except json.JSONDecodeError:
        return jsonify({'error': 'Invalid JSON in menu-data.json'}), 500

# Menü verilerini kaydetme (POST)
@app.route('/api/menu', methods=['POST'])
def save_menu():
    new_menu_data = request.json
    if not new_menu_data:
        return jsonify({'error': 'No JSON data provided'}), 400

    try:
        with open(MENU_FILE, 'w', encoding='utf-8') as f:
            json.dump(new_menu_data, f, ensure_ascii=False, indent=2)
        return jsonify({'message': 'Menu data saved successfully'})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# Statik dosyaları (HTML, CSS, JS, images) sunmak için
@app.route('/<path:filename>')
def static_files(filename):
    # Güvenlik için, sadece belirli dizinlerden dosya sunmak isteyebilirsiniz.
    # Bu örnekte mevcut dizinden sunuyoruz.
    return send_from_directory('.', filename)

if __name__ == '__main__':
    app.run(debug=True, port=5000) # Geliştirme modunda çalıştır, port 5000