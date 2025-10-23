"use client"

import { useState } from "react"
import { ShoppingCart, Search, Menu, X, Home, User } from "lucide-react"
import HomePage from "@/components/pages/home-page"
import ProductDetailsPage from "@/components/pages/product-details-page"
import CartPage from "@/components/pages/cart-page"
import CheckoutPage from "@/components/pages/checkout-page"
import AccountPage from "@/components/pages/account-page"

type Page = "home" | "product" | "cart" | "checkout" | "account"

export default function App() {
  const [currentPage, setCurrentPage] = useState<Page>("home")
  const [cartCount, setCartCount] = useState(3)
  const [selectedProduct, setSelectedProduct] = useState<any>(null)
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)

  const handleProductSelect = (product: any) => {
    setSelectedProduct(product)
    setCurrentPage("product")
  }

  const handleAddToCart = () => {
    setCartCount(cartCount + 1)
  }

  const renderPage = () => {
    switch (currentPage) {
      case "home":
        return <HomePage onProductSelect={handleProductSelect} />
      case "product":
        return (
          <ProductDetailsPage
            product={selectedProduct}
            onAddToCart={handleAddToCart}
            onBack={() => setCurrentPage("home")}
          />
        )
      case "cart":
        return <CartPage onBack={() => setCurrentPage("home")} />
      case "checkout":
        return <CheckoutPage onBack={() => setCurrentPage("cart")} onOrderComplete={() => setCurrentPage("home")} />
      case "account":
        return <AccountPage onBack={() => setCurrentPage("home")} />
      default:
        return <HomePage onProductSelect={handleProductSelect} />
    }
  }

  return (
    <div className="flex flex-col h-screen bg-background">
      {/* Header */}
      <header className="sticky top-0 z-40 bg-primary text-primary-foreground shadow-md">
        <div className="flex items-center justify-between px-4 py-3">
          <div className="flex items-center gap-2">
            <button
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="lg:hidden p-2 hover:bg-primary/80 rounded-lg"
            >
              {mobileMenuOpen ? <X size={24} /> : <Menu size={24} />}
            </button>
            <h1 className="text-xl font-bold">ShopHub</h1>
          </div>

          <div className="flex-1 mx-4 hidden sm:block">
            <div className="relative">
              <Search
                className="absolute left-3 top-1/2 transform -translate-y-1/2 text-primary-foreground/60"
                size={18}
              />
              <input
                type="text"
                placeholder="Search products..."
                className="w-full pl-10 pr-4 py-2 rounded-lg bg-primary/20 text-primary-foreground placeholder-primary-foreground/60 focus:outline-none focus:ring-2 focus:ring-accent"
              />
            </div>
          </div>

          <div className="flex items-center gap-3">
            <button
              onClick={() => setCurrentPage("cart")}
              className="relative p-2 hover:bg-primary/80 rounded-lg transition"
            >
              <ShoppingCart size={24} />
              {cartCount > 0 && (
                <span className="absolute top-0 right-0 bg-accent text-accent-foreground text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                  {cartCount}
                </span>
              )}
            </button>
            <button onClick={() => setCurrentPage("account")} className="p-2 hover:bg-primary/80 rounded-lg transition">
              <User size={24} />
            </button>
          </div>
        </div>

        {/* Mobile Search */}
        <div className="sm:hidden px-4 pb-3">
          <div className="relative">
            <Search
              className="absolute left-3 top-1/2 transform -translate-y-1/2 text-primary-foreground/60"
              size={18}
            />
            <input
              type="text"
              placeholder="Search..."
              className="w-full pl-10 pr-4 py-2 rounded-lg bg-primary/20 text-primary-foreground placeholder-primary-foreground/60 focus:outline-none focus:ring-2 focus:ring-accent"
            />
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="flex-1 overflow-y-auto">{renderPage()}</main>

      {/* Mobile Bottom Navigation */}
      <nav className="fixed bottom-0 left-0 right-0 bg-card border-t border-border lg:hidden">
        <div className="flex justify-around items-center">
          <button
            onClick={() => setCurrentPage("home")}
            className={`flex-1 py-3 flex flex-col items-center gap-1 transition ${
              currentPage === "home" ? "text-primary" : "text-muted-foreground hover:text-foreground"
            }`}
          >
            <Home size={24} />
            <span className="text-xs">Home</span>
          </button>
          <button
            onClick={() => setCurrentPage("cart")}
            className={`flex-1 py-3 flex flex-col items-center gap-1 relative transition ${
              currentPage === "cart" ? "text-primary" : "text-muted-foreground hover:text-foreground"
            }`}
          >
            <ShoppingCart size={24} />
            {cartCount > 0 && (
              <span className="absolute top-1 right-2 bg-accent text-accent-foreground text-xs font-bold rounded-full w-4 h-4 flex items-center justify-center">
                {cartCount}
              </span>
            )}
            <span className="text-xs">Cart</span>
          </button>
          <button
            onClick={() => setCurrentPage("account")}
            className={`flex-1 py-3 flex flex-col items-center gap-1 transition ${
              currentPage === "account" ? "text-primary" : "text-muted-foreground hover:text-foreground"
            }`}
          >
            <User size={24} />
            <span className="text-xs">Account</span>
          </button>
        </div>
      </nav>

      {/* Spacer for mobile nav */}
      <div className="h-20 lg:h-0" />
    </div>
  )
}
