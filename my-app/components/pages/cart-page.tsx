"use client"

import { useState } from "react"
import { ChevronLeft, Trash2, Plus, Minus } from "lucide-react"

interface CartPageProps {
  onBack: () => void
  onCheckout?: () => void
}

const CART_ITEMS = [
  {
    id: 1,
    name: "Premium Wireless Headphones",
    price: 129.99,
    quantity: 1,
    image: "/diverse-people-listening-headphones.png",
  },
  {
    id: 2,
    name: "Organic Cotton T-Shirt",
    price: 34.99,
    quantity: 2,
    image: "/plain-white-tshirt.png",
  },
  {
    id: 3,
    name: "Smart Home LED Bulb",
    price: 24.99,
    quantity: 1,
    image: "/led-bulb.jpg",
  },
]

export default function CartPage({ onBack, onCheckout }: CartPageProps) {
  const [items, setItems] = useState(CART_ITEMS)
  const [promoCode, setPromoCode] = useState("")

  const subtotal = items.reduce((sum, item) => sum + item.price * item.quantity, 0)
  const shipping = subtotal > 50 ? 0 : 9.99
  const tax = subtotal * 0.1
  const total = subtotal + shipping + tax

  const updateQuantity = (id: number, newQuantity: number) => {
    if (newQuantity === 0) {
      setItems(items.filter((item) => item.id !== id))
    } else {
      setItems(items.map((item) => (item.id === id ? { ...item, quantity: newQuantity } : item)))
    }
  }

  return (
    <div className="pb-32">
      {/* Header */}
      <div className="sticky top-0 z-30 bg-card border-b border-border flex items-center gap-3 px-4 py-3">
        <button onClick={onBack} className="p-2 hover:bg-muted rounded-lg transition">
          <ChevronLeft size={24} />
        </button>
        <h2 className="text-lg font-semibold">Shopping Cart</h2>
      </div>

      {items.length === 0 ? (
        <div className="flex flex-col items-center justify-center py-16 px-4">
          <div className="text-6xl mb-4">🛒</div>
          <h3 className="text-xl font-semibold mb-2">Your cart is empty</h3>
          <p className="text-muted-foreground text-center mb-6">Add some items to get started!</p>
          <button
            onClick={onBack}
            className="bg-primary text-primary-foreground px-6 py-2 rounded-lg font-medium hover:bg-primary/90 transition"
          >
            Continue Shopping
          </button>
        </div>
      ) : (
        <>
          {/* Cart Items */}
          <div className="p-4 space-y-3">
            {items.map((item) => (
              <div key={item.id} className="flex gap-3 bg-card border border-border rounded-lg p-3">
                <img
                  src={item.image || "/placeholder.svg"}
                  alt={item.name}
                  className="w-20 h-20 rounded-lg object-cover"
                />
                <div className="flex-1">
                  <h3 className="font-semibold text-sm mb-1">{item.name}</h3>
                  <p className="text-primary font-bold mb-2">${item.price}</p>
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2 bg-muted rounded-lg">
                      <button
                        onClick={() => updateQuantity(item.id, item.quantity - 1)}
                        className="p-1 hover:bg-border rounded transition"
                      >
                        <Minus size={16} />
                      </button>
                      <span className="px-2 font-medium text-sm">{item.quantity}</span>
                      <button
                        onClick={() => updateQuantity(item.id, item.quantity + 1)}
                        className="p-1 hover:bg-border rounded transition"
                      >
                        <Plus size={16} />
                      </button>
                    </div>
                    <button
                      onClick={() => updateQuantity(item.id, 0)}
                      className="p-2 text-destructive hover:bg-destructive/10 rounded-lg transition"
                    >
                      <Trash2 size={18} />
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>

          {/* Promo Code */}
          <div className="px-4 py-3 space-y-2">
            <label className="text-sm font-medium">Promo Code</label>
            <div className="flex gap-2">
              <input
                type="text"
                placeholder="Enter code"
                value={promoCode}
                onChange={(e) => setPromoCode(e.target.value)}
                className="flex-1 px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
              />
              <button className="bg-primary text-primary-foreground px-4 py-2 rounded-lg font-medium hover:bg-primary/90 transition">
                Apply
              </button>
            </div>
          </div>

          {/* Order Summary */}
          <div className="fixed bottom-0 left-0 right-0 bg-card border-t border-border p-4 space-y-3">
            <div className="space-y-2 text-sm">
              <div className="flex justify-between">
                <span className="text-muted-foreground">Subtotal</span>
                <span className="font-medium">${subtotal.toFixed(2)}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-muted-foreground">Shipping</span>
                <span className="font-medium">{shipping === 0 ? "FREE" : `$${shipping.toFixed(2)}`}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-muted-foreground">Tax</span>
                <span className="font-medium">${tax.toFixed(2)}</span>
              </div>
              <div className="border-t border-border pt-2 flex justify-between">
                <span className="font-semibold">Total</span>
                <span className="text-lg font-bold text-primary">${total.toFixed(2)}</span>
              </div>
            </div>

            <button
              onClick={onCheckout}
              className="w-full bg-accent text-accent-foreground py-3 rounded-lg font-semibold hover:bg-accent/90 transition"
            >
              Proceed to Checkout
            </button>
          </div>
        </>
      )}
    </div>
  )
}
