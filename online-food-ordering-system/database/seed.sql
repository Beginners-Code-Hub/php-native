-- Demo data only. Every password is: Demo@1234
USE fooddash;

INSERT INTO users (id, name, email, phone, password_hash, role) VALUES
 (1, 'Admin Demo',    'admin@example.com',  '0917 000 0001', '$2y$10$Dky3EDTXjUB5R1Zz7.cvWujxSZYPrCEZammC4oIVYEGuToe92FXRS', 'admin'),
 (2, 'Rico Rider',    'rider@example.com',  '0917 000 0002', '$2y$10$Dky3EDTXjUB5R1Zz7.cvWujxSZYPrCEZammC4oIVYEGuToe92FXRS', 'rider'),
 (3, 'Mara Rider',    'rider2@example.com', '0917 000 0003', '$2y$10$Dky3EDTXjUB5R1Zz7.cvWujxSZYPrCEZammC4oIVYEGuToe92FXRS', 'rider'),
 (4, 'Demo Customer', 'demo@example.com',   '0917 000 0004', '$2y$10$Dky3EDTXjUB5R1Zz7.cvWujxSZYPrCEZammC4oIVYEGuToe92FXRS', 'customer'),
 (5, 'Alex Sample',   'alex@example.com',   '0917 000 0005', '$2y$10$Dky3EDTXjUB5R1Zz7.cvWujxSZYPrCEZammC4oIVYEGuToe92FXRS', 'customer');

INSERT INTO categories (id, name, icon) VALUES
 (1, 'Burgers', 'lunch_dining'), (2, 'Pizza', 'local_pizza'), (3, 'Rice Meals', 'rice_bowl'),
 (4, 'Drinks', 'local_cafe'),    (5, 'Desserts', 'icecream');

INSERT INTO products (id, category_id, name, description, price, emoji) VALUES
 (1,  1, 'Classic Cheeseburger', 'Beef patty, cheddar, pickles, house sauce',     129.00, '🍔'),
 (2,  1, 'Double Bacon Burger',  'Two patties, crispy bacon, caramelised onion', 189.00, '🥓'),
 (3,  1, 'Crispy Chicken Burger','Buttermilk fried chicken, slaw, spicy mayo',   149.00, '🍗'),
 (4,  2, 'Margherita Pizza',     'Tomato, mozzarella, fresh basil',              279.00, '🍕'),
 (5,  2, 'Pepperoni Pizza',      'Loaded with pepperoni and extra cheese',       329.00, '🍕'),
 (6,  2, 'Hawaiian Pizza',       'Ham, pineapple and mozzarella',                299.00, '🍍'),
 (7,  3, 'Chicken Adobo Rice',   'Slow-braised adobo, garlic rice, egg',         119.00, '🍛'),
 (8,  3, 'Pork Sisig Rice',      'Sizzling sisig, rice, calamansi',              139.00, '🍳'),
 (9,  3, 'Beef Tapa Silog',      'Garlic beef tapa, fried rice, egg',            129.00, '🥩'),
 (10, 4, 'Iced Milk Tea',        'Brown sugar milk tea, 16 oz',                   79.00, '🧋'),
 (11, 4, 'Fresh Lemonade',       'Squeezed lemon, mint, ice',                     69.00, '🍋'),
 (12, 4, 'Iced Coffee',          'Cold brew with milk',                           89.00, '☕'),
 (13, 5, 'Halo-Halo',            'Shaved ice, sweet beans, leche flan, ube',      99.00, '🍧'),
 (14, 5, 'Chocolate Lava Cake',  'Warm centre, vanilla ice cream',               119.00, '🍰'),
 (15, 5, 'Mango Float',          'Graham layers, cream, ripe mango',              89.00, '🥭');

-- Past delivered orders so the sales report has numbers.
INSERT INTO orders (id, user_id, rider_id, status, subtotal, delivery_fee, total, area, address, phone, dest_x, dest_y, created_at) VALUES
 (1, 4, 2, 'delivered', 387.00, 40, 427.00, 'Poblacion',       '12 Mabini St.',       '0917 000 0004', 70, 82, NOW() - INTERVAL 3 DAY),
 (2, 5, 3, 'delivered', 279.00, 40, 319.00, 'Riverside',       '5 Rizal Ave.',        '0917 000 0005', 30, 48, NOW() - INTERVAL 2 DAY),
 (3, 4, 2, 'delivered', 596.00, 40, 636.00, 'Central Plaza',   'Unit 3, Plaza Bldg.', '0917 000 0004', 52, 22, NOW() - INTERVAL 1 DAY),
 (4, 5, 3, 'delivered', 208.00, 40, 248.00, 'Sunrise Village', 'Lot 8, Sunrise',      '0917 000 0005', 82, 52, NOW() - INTERVAL 5 HOUR);

INSERT INTO order_items (order_id, product_id, name, qty, unit_price) VALUES
 (1, 1,  'Classic Cheeseburger', 3, 129.00),
 (2, 4,  'Margherita Pizza',     1, 279.00),
 (3, 5,  'Pepperoni Pizza',      1, 329.00), (3, 13, 'Halo-Halo', 1, 99.00),
 (3, 10, 'Iced Milk Tea',        1,  79.00), (3, 12, 'Iced Coffee', 1, 89.00),
 (4, 7,  'Chicken Adobo Rice',   1, 119.00), (4, 15, 'Mango Float', 1, 89.00);

INSERT INTO order_status_log (order_id, status, changed_at) VALUES
 (1, 'pending', NOW() - INTERVAL 3 DAY), (1, 'delivered', NOW() - INTERVAL 3 DAY + INTERVAL 40 MINUTE),
 (2, 'pending', NOW() - INTERVAL 2 DAY), (2, 'delivered', NOW() - INTERVAL 2 DAY + INTERVAL 35 MINUTE),
 (3, 'pending', NOW() - INTERVAL 1 DAY), (3, 'delivered', NOW() - INTERVAL 1 DAY + INTERVAL 45 MINUTE),
 (4, 'pending', NOW() - INTERVAL 5 HOUR), (4, 'delivered', NOW() - INTERVAL 5 HOUR + INTERVAL 30 MINUTE);
