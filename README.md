# 🛍️ O-IN E-Commerce System

> A smart, AI-integrated online shopping platform for seamless customer experience and efficient backend management.

---

## 📘 Overview
The **O-IN E-Commerce System** is a modern and fully integrated e-commerce platform that combines **AI-driven customer service**, **Google OAuth login**, and **comprehensive order management**.  
It enhances user experience while simplifying administrative tasks, inspired by the original O-IN brand but extended into a complete, intelligent solution for both customers and administrators.

---

## 🎯 Objectives
- Build a **user-friendly member system** with Google Login support.  
- Integrate **AI assistance** to guide users and boost interaction.  
- Provide a **robust backend interface** for managing products, orders, and warehouses.  
- Maintain **real-time synchronization** across databases to ensure data consistency.

---

## 🏗️ System Architecture
The project consists of two major components:

1. **Frontend Website** – Displays products, promotions, news, and allows member login, registration, and order checkout.  
2. **Backend Management System** – Handles member, product, order, warehouse, and AI prompt management with an intuitive interface.

---

## 🌟 Highlight Features

### 🔐 Google OAuth Login Integration
Users can register or log in through their Google accounts.  
Upon first login, the system automatically fetches the user’s email and prompts for additional profile completion — balancing convenience and data integrity.

### 🤖 AI Assistant Powered by Gemini API
The built-in AI assistant answers natural language queries like:
> “Where can I find suitcases?” or “Are there any current discounts?”

The AI dynamically provides:
- Relevant **product links** or **category pages**
- **Discount code suggestions**
- **Smart replies** customized by admin-defined prompts  

Administrators can **edit and manage AI prompts** in real time via the backend system — enabling flexible customer support and marketing strategies.

### 🧠 Intelligent Backend Management
- Full CRUD for **Products**, **Members**, **Orders**, **News**, and **Warehouses**  
- **AI Assistant Management** for prompt updates and response templates  
- **Discount Code Management** and **Repair/Warranty Tracking**  
- Built-in **data validation** and **status synchronization**

### 🔄 Auto-Sync Inventory System
The product stock (`products.storage`) automatically synchronizes with `vwarehouse_products`, ensuring that all data remains consistent between warehouse records and storefront availability.

### 🧾 Online Repair & Warranty Requests
Customers can file repair requests online and check their progress through the system — simulating a real-world after-sales service process.

---

## 🧩 Database Design
The system database is built on **MySQL** and includes:
`admin`, `members`, `products`, `orders`, `orders_product`,  
`discount_code`, `vwarehouse`, `vwarehouse_products`, and `repair`.

All tables are linked through a carefully designed **ER-Model**, ensuring data integrity and efficient querying between frontend and backend.

---

## 💡 Tech Stack
| Layer | Technologies |
|-------|---------------|
| **Frontend** | HTML, CSS, JavaScript |
| **Backend** | PHP / Python (FastAPI) |
| **Database** | MySQL |
| **AI Integration** | Gemini API (Dynamic Prompt System) |
| **Login System** | Google OAuth 2.0 |
| **Version Control** | Git & GitHub |

---

## 📄 License
This project was developed for educational purposes as part of the **Database Systems (1132)** course.  
All rights reserved © 2025 O-IN Development Team.
