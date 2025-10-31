# DatabaseAllin
🛍️ O-IN E-Commerce System
📘 Overview
The O-IN E-Commerce System is a full-featured online shopping platform designed to enhance both user convenience and administrative efficiency.
Originally inspired by the official O-IN website, this project expands its functionality by integrating member systems, AI assistance, and order management, creating a more complete and user-friendly shopping experience.
🎯 Objectives
The main goal of this project is to:
Introduce a member system for better order tracking and personalization.
Implement AI assistance to help users quickly locate desired products.
Provide an intuitive backend management interface to streamline business operations.
🏗️ System Architecture
The system is divided into two main parts:
Frontend Website – Displays products, news, promotions, and supports user registration, login, and shopping cart features.
Backend Management System – Allows administrators to manage members, products, orders, warehouses, and AI prompts efficiently.
⚙️ Main Features
🧾 General Features
Home Page: Includes advertisements, latest news, and social media links.
User System: Supports both normal registration and Google sign-in.
Shopping Cart: Add, modify, and confirm orders before checkout.
Order Tracking: Registered members can view their purchase history.
🛠️ Backend Management
CRUD for Products, Members, Orders, News, and Warehouses.
Discount Code Management: Add and manage promotional discounts.
Repair/Warranty System: Submit and track repair reports online.
AI Assistant Management: Adjust AI prompts dynamically for customer service or promotions.
🤖 AI Assistant
The AI helper answers questions such as:
“Where can I find suitcases?” or “Are there any recent discount codes?”
It automatically provides relevant responses and links to the proper sections or products.
🧩 Database Design
Includes core tables such as:
admin, members, products, orders, orders_product,
discount_code, vwarehouse, vwarehouse_products, repair.
All data relationships are modeled using an ER-Model, ensuring consistent integration between frontend and backend.
💡 Tech Stack
Frontend: HTML, CSS, JavaScript
Backend: PHP / Python (FastAPI)
Database: MySQL
AI Integration: Gemini API (dynamic prompt management)
Version Control: Git & GitHub
📄 License
This project is developed for educational purposes as part of the Database Systems (1132) course project.
