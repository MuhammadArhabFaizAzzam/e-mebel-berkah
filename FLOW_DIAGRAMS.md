# 🗺️ MULTI-SELLER SYSTEM - FLOW DIAGRAMS

---

## 1️⃣ USER FLOW - SELLER JOURNEY

```
┌─────────────────────────────────────────────────────────┐
│                    START: User Login                    │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │  Dashboard (Buyer)     │
        │ - View all products    │
        │ - Add to cart          │
        │ - Browse categories    │
        └────────────────────────┘
                     │
        ┌────────────┴────────────┐
        │                         │
        ▼                         ▼
  ┌──────────────┐        ┌──────────────────────┐
  │  Continue    │        │  Click "Mulai        │
  │  Shopping    │        │  Berjualan"          │
  └──────────────┘        └──────────┬───────────┘
        │                            │
        │                            ▼
        │                  ┌──────────────────────┐
        │                  │ Seller Dashboard     │
        │                  │ - View my products   │
        │                  │ - Statistics         │
        │                  └──────┬───────────────┘
        │                         │
        │              ┌──────────┴──────────┬─────────────┐
        │              │                     │             │
        │              ▼                     ▼             ▼
        │    ┌──────────────────┐  ┌──────────────┐  ┌──────────────┐
        │    │ Add New Product  │  │ Edit Product │  │ Delete       │
        │    │ 1. Fill form     │  │ 1. Click edit│  │ Product      │
        │    │ 2. Upload image  │  │ 2. Update   │  │ 1. Confirm   │
        │    │ 3. Submit        │  │ 3. Save     │  │ 2. Delete    │
        │    └────────┬─────────┘  └──────┬───────┘  └────────┬─────┘
        │             │                    │                  │
        │             └────────┬───────────┴──────────────────┘
        │                      │
        │                      ▼
        │         ┌──────────────────────────┐
        │         │ Back to Seller Dashboard │
        │         │ - See updated list       │
        │         └──────┬───────────────────┘
        │                │
        └────────────────┴─────────────────────────►
                         │
                         ▼
                  ┌──────────────┐
                  │  Dashboard   │
                  │   (Buyer)    │
                  │ See my new   │
                  │ product      │
                  │ selling!     │
                  └──────────────┘
```

---

## 2️⃣ PRODUCT LIFECYCLE

```
┌─────────────────────────────────────────────────────────┐
│                  PRODUCT LIFECYCLE                      │
└─────────────────────────────────────────────────────────┘

1. CREATION
   │
   User fills form
   ├─ Name
   ├─ Category
   ├─ Price
   ├─ Discount (optional)
   ├─ Stock
   ├─ Description
   └─ Image
   │
   ▼
2. VALIDATION
   │
   ├─ All required fields? ✓
   ├─ Image format valid? ✓
   ├─ File size < 5MB? ✓
   └─ Price > 0? ✓
   │
   ▼
3. STORAGE
   │
   ├─ Insert to database
   │  └─ seller_id = user ID
   │  └─ status = 'active'
   │
   ├─ Save image
   │  └─ Folder: img/products/
   │  └─ Name: timestamp_unique.jpg
   │
   └─ Confirmation
   │
   ▼
4. VISIBILITY
   │
   ├─ Status = 'active'
   │  └─ Shows in buyer dashboard ✓
   │  └─ Shows in landing page ✓
   │  └─ Can be purchased ✓
   │
   ├─ Status = 'inactive'
   │  └─ Hidden from buyers ✗
   │  └─ Owner can still see ✓
   │
   ├─ Status = 'pending'
   │  └─ Awaiting approval ⏳
   │
   ▼
5. MODIFICATION
   │
   Editor clicks Edit
   │
   ├─ Load current data
   ├─ Show image preview
   ├─ Allow all changes
   │
   └─ Save changes
      ├─ Update database
      ├─ Delete old image if new uploaded
      └─ Confirm update
   │
   ▼
6. DELETION
   │
   Editor clicks Delete
   │
   ├─ Confirmation? (required)
   ├─ Delete from database
   ├─ Delete image from disk
   │
   └─ Product gone permanently
```

---

## 3️⃣ DATABASE RELATIONSHIPS

```
┌──────────────────────────────────────────────────────────────┐
│                    DATABASE SCHEMA                           │
└──────────────────────────────────────────────────────────────┘

USERS TABLE
┌─────────────────────────────────┐
│ id (PK)                         │
│ name                            │
│ email                           │
│ password                        │
│ phone                           │
│ address                         │
│ created_at                      │
└──────────────┬──────────────────┘
               │
               │ ONE-TO-MANY
               │
               ▼
PRODUCTS TABLE
┌──────────────────────────────────┐
│ id (PK)                          │
│ seller_id (FK) ────────┐         │
│ name                   │         │
│ category               │         │
│ description            │         │
│ price                  │         │
│ discount_price         │         │
│ stock                  │         │
│ image                  │         │
│ status (active/...)    │         │
│ created_at             │         │
└──────────────┬──────────┤         │
               │          │         │
               │          │◄────────┘
               │          │
               │          └─ Points to users.id
               │
               │
               │ ONE-TO-MANY
               │
               ▼
ORDERS TABLE
┌──────────────────────────────┐
│ id (PK)                      │
│ user_id (FK) ──────┐         │
│ total_amount       │         │
│ status             │         │
│ created_at         │         │
└────────┬───────────┤         │
         │           │         │
         │           │◄────────┘
         │           │
         │           └─ Buyer's user_id
         │
         │ ONE-TO-MANY
         │
         ▼
ORDER_ITEMS TABLE
┌────────────────────────────┐
│ id (PK)                    │
│ order_id (FK)      ────┐   │
│ product_id (FK)    ──┐ │   │
│ product_name       │ │ │   │
│ product_price      │ │ │   │
│ quantity           │ │ │   │
│ subtotal           │ │ │   │
└──────────┬──────────┼─┼─┬──┘
           │          │ │ │
           │          │ │ └──────────┐
           │          │ │            │
           │          │ └─ From products table
           │          └─ From orders table
           │
           └─ Many items per order
```

---

## 4️⃣ BUYER FLOW - MULTI-SELLER SHOPPING

```
BUYER JOURNEY
┌──────────────────────────────────────────────────────┐
│               User Login                             │
└──────────────┬───────────────────────────────────────┘
               │
               ▼
        ┌──────────────────────┐
        │ Dashboard            │
        │ Shows ALL products   │
        │ from ALL sellers     │
        └──────────┬───────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
        ▼                     ▼
   ┌─────────────┐     ┌──────────────┐
   │ Browse by   │     │ Add to Cart  │
   │ Category    │     │ from Seller 1│
   │             │     └──────┬───────┘
   └──────────┬──┘            │
              │               ▼
              │      ┌──────────────────┐
              │      │ Add more products│
              │      │ from Seller 2,3..│
              │      │ to same cart     │
              │      └──────┬───────────┘
              │             │
              └─────┬───────┘
                    │
                    ▼
           ┌────────────────────┐
           │ Shopping Cart      │
           │ Product A (Seller1)│
           │ Product B (Seller2)│
           │ Product C (Seller1)│
           │ Total: Rp XXX,XXX  │
           └────────┬───────────┘
                    │
                    ▼
           ┌────────────────────┐
           │ Checkout           │
           │ Review order       │
           │ Select payment     │
           └────────┬───────────┘
                    │
                    ▼
           ┌────────────────────┐
           │ Order Created      │
           │ Multi-seller order │
           │ sent to all seller │
           └────────────────────┘

SELLER SIDE (Future)
    ↓
Seller 1 prepares order A+C
Seller 2 prepares order B
    ↓
Buyer gets notification
    ↓
Buyer receives all items
```

---

## 5️⃣ FILE UPLOAD PROCESS

```
FILE UPLOAD FLOW
┌──────────────────────────────────────────────────────┐
│              User selects image file                 │
└──────────────┬───────────────────────────────────────┘
               │
               ▼
        ┌──────────────────┐
        │ Client-side      │
        │ validation       │
        │ (by browser)     │
        │ File type? ✓     │
        └────────┬─────────┘
                 │
                 ▼
        ┌────────────────────────┐
        │ Form submitted to      │
        │ seller_process.php     │
        └────────┬───────────────┘
                 │
                 ▼
        ┌────────────────────────┐
        │ Server validation      │
        │ 1. File exists? ✓      │
        │ 2. Type valid? ✓       │
        │ 3. Size < 5MB? ✓       │
        │ 4. No errors? ✓        │
        └────────┬───────────────┘
                 │
              ┌──┴─────────────────┐
              │                    │
          PASS ▼              FAIL ▼
              │                    │
      ┌───────┴────────┐    ┌──────┴────────┐
      │ Generate name  │    │ Show error    │
      │ timestamp_     │    │ message       │
      │ unique.jpg     │    │ (invalid)     │
      └───────┬────────┘    └───────────────┘
              │
              ▼
      ┌───────────────────────┐
      │ Move file to          │
      │ img/products/         │
      │ folder                │
      │ timestamp_unique.jpg  │
      └───────┬───────────────┘
              │
              ▼
      ┌───────────────────────┐
      │ Save path to          │
      │ database              │
      │ IMG/products/filename │
      └───────┬───────────────┘
              │
              ▼
      ┌───────────────────────┐
      │ ✅ Success!           │
      │ Show in table         │
      │ Product created       │
      └───────────────────────┘
```

---

## 6️⃣ SELLER DASHBOARD STATE MACHINE

```
SELLER DASHBOARD STATES
┌────────────────────────────────────────────────────────┐
│                 Initial State                          │
│           seller_dashboard.php loaded                  │
│          Query products WHERE seller_id = X           │
└──────────────┬─────────────────────────────────────────┘
               │
      ┌────────┴────────┬─────────────┐
      │                 │             │
      ▼                 ▼             ▼
  ┌──────────┐   ┌────────────┐ ┌──────────┐
  │No products│   │Some product│ │Many prod │
  │(empty)   │   │(1-10)      │ │(10+)     │
  │- Show    │   │- List all  │ │- Paginate│
  │  message │   │- Show stats│ │- Sort    │
  │- Prompt  │   │- Full      │ │- Filter  │
  │  to add  │   │  features  │ │          │
  └──────────┘   └────────────┘ └──────────┘
      │                 │             │
      └────────┬────────┴─────────────┘
               │
        ┌──────┴──────────────────┐
        │                         │
   USER ACTION              USER ACTION
        │                         │
        ▼                         ▼
  CLICK "Tambah"         CLICK on Product
  Product Baru                │
        │                     ├─ Edit ──────┐
        ▼                     │             │
  ┌──────────────┐            ├─ Delete ────┼─────┐
  │Modal form    │            │             │     │
  │- Fill fields │            └─ Status ────┘     │
  │- Upload image│                                │
  │- Submit      │                                │
  └──────┬───────┘                                │
         │                                        │
         ▼                                        │
  ┌──────────────┐        ┌──────────────┐      │
  │Save to DB    │        │Edit form     │      │
  │Create product│        │Pre-filled    │      │
  └──────┬───────┘        │Submit update │      │
         │                └──────┬───────┘      │
         │                       │              │
         └───────┬───────────────┴──┐           │
                 │                  │           │
                 ▼                  ▼           ▼
        ┌─────────────────────────────────────────────┐
        │   Refresh Dashboard                         │
        │   - Update product list                     │
        │   - Update statistics                       │
        │   - Show success message                    │
        │   - Clear form                              │
        └─────────────────────────────────────────────┘
```

---

## 7️⃣ STATUS FLOW

```
PRODUCT STATUS LIFECYCLE
┌─────────────────────────────────────────────────┐
│           Product Created                       │
│           status = 'active' (default)           │
└────────────────┬────────────────────────────────┘
                 │
         ┌───────┴───────┐
         │               │
         ▼               ▼
    ┌─────────┐    ┌──────────┐
    │ ACTIVE  │    │ INACTIVE │
    │         │    │          │
    │Visible  │    │Hidden    │
    │to all   │    │from      │
    │buyers   │    │buyers    │
    │         │    │          │
    │Can be   │    │Cannot be │
    │bought   │    │bought    │
    └────┬────┘    └──────────┘
         │              │
    Owner clicks        │
    Toggle button       │
         │──────────────┘
         │
    ┌────┴──────┐
    │            │
    ▼            ▼
INACTIVE  →   ACTIVE
  (show     (show
  toggle)   toggle)
    │            │
    └────────────┘
         │
    Repeat as needed
```

---

## 8️⃣ SECURITY FLOW

```
SECURITY VALIDATION FLOW
┌───────────────────────────────────────────┐
│        User tries action                  │
│    (edit/delete/upload)                   │
└──────────────┬────────────────────────────┘
               │
               ▼
        ┌──────────────────┐
        │ Check session    │
        │ Is user logged?  │
        └────────┬─────────┘
                 │
              ┌──┴────────┐
           YES│           │NO
              ▼           ▼
         Continue    Redirect
                     to login
              │
              ▼
        ┌──────────────────┐
        │Check ownership   │
        │product.seller_id │
        │== $_SESSION      │
        │['user_id']       │
        └────────┬─────────┘
                 │
              ┌──┴────────┐
           YES│           │NO
              ▼           ▼
         Continue    Reject
                     Access Denied
              │
              ▼
        ┌──────────────────┐
        │Validate input    │
        │- Data type       │
        │- Required fields │
        │- File type       │
        │- File size       │
        └────────┬─────────┘
                 │
              ┌──┴────────┐
           YES│           │NO
              ▼           ▼
         Process     Show Error
         request
              │
              ▼
        ┌──────────────────┐
        │Execute action    │
        │(prepared query)  │
        │(file operations) │
        └────────┬─────────┘
                 │
                 ▼
        ┌──────────────────┐
        │Return result     │
        │Success/Error msg │
        └──────────────────┘
```

---

## 9️⃣ DATABASE QUERY FLOW

```
QUERY FLOW
┌─────────────────────────────────────────────────────┐
│          Buyer views dashboard                      │
└──────────────┬────────────────────────────────────────┘
               │
               ▼
        ┌──────────────────────────┐
        │ dashboard.php            │
        │ SELECT * FROM products   │
        │ WHERE status = 'active'  │
        └────────┬─────────────────┘
                 │
              ┌──┴────────────────────┐
          SUCCESS│                    │ERROR
              ▼                        ▼
        ┌────────────────┐    ┌───────────────┐
        │ Get from DB    │    │ Use fallback  │
        │ - Product 1    │    │ array         │
        │ - Product 2    │    │ (hardcoded)   │
        │ - Product 3    │    │               │
        │   from all     │    │ No downtime!  │
        │   sellers      │    │               │
        └────────┬───────┘    └───────┬───────┘
                 │                    │
                 └────────┬───────────┘
                          │
                          ▼
                ┌───────────────────────┐
                │ Display products      │
                │ - Calculate discounts │
                │ - Show prices         │
                │ - Render UI           │
                │ - Enable buying       │
                └───────────────────────┘
```

---

## 🔟 COMPLETE USER JOURNEY

```
┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃         COMPLETE MULTI-SELLER WORKFLOW          ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

MORNING: User A (Furniture Seller)
─────────────────────────────────────
9:00 - Login dashboard
9:05 - Click "Mulai Berjualan"
9:10 - Upload new chair product (seller_dashboard.php)
9:15 - Edit pricing, add discount
9:20 - Update product image
9:25 - Set status to ACTIVE
9:30 - View all my products in seller dashboard
9:35 - Approve, it shows in buyer view!

AFTERNOON: User B (Buyer)
─────────────────────────────────────
2:00 - Login dashboard
2:05 - Browse all products (from A + others)
2:10 - Find User A's new chair
2:15 - Add to cart (from Seller A)
2:20 - Continue shopping
2:25 - Find table from Seller C
2:30 - Add to cart (from Seller C)
2:35 - Find sofa from Seller A
2:40 - Add to cart (from Seller A again)
2:45 - Go to cart (3 items, 2 sellers)
2:50 - Checkout (all sellers included)
2:55 - Order placed!
3:00 - Both sellers notified of orders

EVENING: User A (as Seller)
─────────────────────────────────────
5:00 - Check seller dashboard
5:05 - See statistics updated
5:10 - See order from User B
5:15 - Prepare items
5:20 - Mark as shipped
5:25 - Continue selling other products

SYSTEM CONTINUES:
─────────────────────────────────────
- Many sellers uploading products
- Many buyers shopping
- Cart works with multi-seller
- All prices calculated correctly
- All images stored properly
- All products discoverable
- All transactions processed
```

---

**Visual guides complete!** 📊

These diagrams show every aspect of the system flow.

