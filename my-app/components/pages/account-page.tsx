"use client"

import { useState } from "react"
import {
  ChevronLeft,
  LogOut,
  Package,
  MapPin,
  CreditCard,
  Bell,
  Settings,
  ChevronRight,
  Trash2,
  Plus,
  Edit2,
} from "lucide-react"

interface AccountPageProps {
  onBack: () => void
}

type AccountSection = "main" | "orders" | "addresses" | "payments" | "notifications" | "settings"

const SAMPLE_ORDERS = [
  {
    id: "#ORD-001",
    date: "Oct 20, 2024",
    status: "Delivered",
    amount: "$189.98",
    items: 3,
    trackingNumber: "TRK123456789",
    estimatedDelivery: "Oct 20, 2024",
    details: [
      { name: "Premium Wireless Headphones", price: "$129.99", qty: 1 },
      { name: "Organic Cotton T-Shirt", price: "$34.99", qty: 2 },
      { name: "Smart Home LED Bulb", price: "$24.99", qty: 1 },
    ],
  },
  {
    id: "#ORD-002",
    date: "Oct 15, 2024",
    status: "In Transit",
    amount: "$59.99",
    items: 1,
    trackingNumber: "TRK987654321",
    estimatedDelivery: "Oct 22, 2024",
    details: [{ name: "Professional Yoga Mat", price: "$59.99", qty: 1 }],
  },
]

const SAMPLE_ADDRESSES = [
  {
    id: 1,
    type: "Home",
    name: "John Doe",
    address: "123 Main Street",
    city: "New York",
    state: "NY",
    zipCode: "10001",
    country: "United States",
    isDefault: true,
  },
  {
    id: 2,
    type: "Work",
    name: "John Doe",
    address: "456 Business Ave",
    city: "New York",
    state: "NY",
    zipCode: "10002",
    country: "United States",
    isDefault: false,
  },
]

const SAMPLE_PAYMENTS = [
  {
    id: 1,
    type: "Visa",
    last4: "4242",
    expiry: "12/25",
    isDefault: true,
  },
  {
    id: 2,
    type: "Mastercard",
    last4: "5555",
    expiry: "08/26",
    isDefault: false,
  },
]

export default function AccountPage({ onBack }: AccountPageProps) {
  const [isLoggedIn, setIsLoggedIn] = useState(true)
  const [currentSection, setCurrentSection] = useState<AccountSection>("main")
  const [selectedOrder, setSelectedOrder] = useState<string | null>(null)

  if (!isLoggedIn) {
    return (
      <div className="flex flex-col items-center justify-center py-16 px-4">
        <div className="text-6xl mb-4">👤</div>
        <h3 className="text-xl font-semibold mb-2">Sign In to Your Account</h3>
        <p className="text-muted-foreground text-center mb-6">Access your orders, addresses, and more</p>
        <button className="bg-primary text-primary-foreground px-6 py-2 rounded-lg font-medium hover:bg-primary/90 transition mb-3 w-full">
          Sign In
        </button>
        <button className="bg-muted text-foreground px-6 py-2 rounded-lg font-medium hover:bg-muted/80 transition w-full">
          Create Account
        </button>
      </div>
    )
  }

  const handleBack = () => {
    if (currentSection !== "main") {
      setCurrentSection("main")
      setSelectedOrder(null)
    } else {
      onBack()
    }
  }

  return (
    <div className="pb-8">
      {/* Header */}
      <div className="sticky top-0 z-30 bg-card border-b border-border flex items-center gap-3 px-4 py-3">
        <button onClick={handleBack} className="p-2 hover:bg-muted rounded-lg transition">
          <ChevronLeft size={24} />
        </button>
        <h2 className="text-lg font-semibold">
          {currentSection === "main"
            ? "My Account"
            : currentSection === "orders"
              ? "My Orders"
              : currentSection === "addresses"
                ? "Addresses"
                : currentSection === "payments"
                  ? "Payment Methods"
                  : currentSection === "notifications"
                    ? "Notifications"
                    : "Settings"}
        </h2>
      </div>

      {/* Main Account View */}
      {currentSection === "main" && (
        <>
          {/* User Profile */}
          <div className="p-4 bg-gradient-to-r from-primary to-accent text-primary-foreground rounded-lg m-4">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-16 h-16 bg-primary-foreground/20 rounded-full flex items-center justify-center text-2xl">
                👤
              </div>
              <div>
                <h3 className="text-lg font-bold">John Doe</h3>
                <p className="text-sm opacity-90">john@example.com</p>
              </div>
            </div>
            <button className="text-sm font-medium hover:underline">Edit Profile</button>
          </div>

          {/* Menu Items */}
          <div className="px-4 space-y-2">
            {[
              { icon: Package, label: "My Orders", badge: "2 pending", section: "orders" as AccountSection },
              { icon: MapPin, label: "Addresses", badge: "3 saved", section: "addresses" as AccountSection },
              { icon: CreditCard, label: "Payment Methods", badge: "2 cards", section: "payments" as AccountSection },
              { icon: Bell, label: "Notifications", badge: "On", section: "notifications" as AccountSection },
              { icon: Settings, label: "Settings", badge: null, section: "settings" as AccountSection },
            ].map((item, i) => (
              <button
                key={i}
                onClick={() => setCurrentSection(item.section)}
                className="w-full flex items-center justify-between p-4 bg-card border border-border rounded-lg hover:bg-muted transition"
              >
                <div className="flex items-center gap-3">
                  <item.icon className="text-primary" size={24} />
                  <span className="font-medium">{item.label}</span>
                </div>
                <div className="flex items-center gap-2">
                  {item.badge && (
                    <span className="text-xs bg-accent text-accent-foreground px-2 py-1 rounded-full font-medium">
                      {item.badge}
                    </span>
                  )}
                  <ChevronRight size={20} className="text-muted-foreground" />
                </div>
              </button>
            ))}
          </div>

          {/* Recent Orders */}
          <div className="px-4 mt-6">
            <h3 className="text-lg font-semibold mb-3">Recent Orders</h3>
            <div className="space-y-3">
              {SAMPLE_ORDERS.map((order, i) => (
                <div key={i} className="p-3 bg-card border border-border rounded-lg">
                  <div className="flex items-center justify-between mb-2">
                    <span className="font-semibold text-sm">{order.id}</span>
                    <span
                      className={`text-xs font-medium px-2 py-1 rounded-full ${
                        order.status === "Delivered" ? "bg-success/20 text-success" : "bg-accent/20 text-accent"
                      }`}
                    >
                      {order.status}
                    </span>
                  </div>
                  <div className="flex items-center justify-between text-sm">
                    <span className="text-muted-foreground">{order.date}</span>
                    <span className="font-bold">{order.amount}</span>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Logout */}
          <div className="px-4 mt-6">
            <button
              onClick={() => setIsLoggedIn(false)}
              className="w-full flex items-center justify-center gap-2 py-3 bg-destructive text-destructive-foreground rounded-lg font-medium hover:bg-destructive/90 transition"
            >
              <LogOut size={20} />
              Sign Out
            </button>
          </div>
        </>
      )}

      {/* Orders View */}
      {currentSection === "orders" && !selectedOrder && (
        <div className="p-4 space-y-3">
          {SAMPLE_ORDERS.map((order) => (
            <button
              key={order.id}
              onClick={() => setSelectedOrder(order.id)}
              className="w-full p-4 bg-card border border-border rounded-lg hover:bg-muted transition text-left"
            >
              <div className="flex items-center justify-between mb-2">
                <span className="font-semibold">{order.id}</span>
                <span
                  className={`text-xs font-medium px-2 py-1 rounded-full ${
                    order.status === "Delivered" ? "bg-success/20 text-success" : "bg-accent/20 text-accent"
                  }`}
                >
                  {order.status}
                </span>
              </div>
              <div className="flex items-center justify-between text-sm mb-2">
                <span className="text-muted-foreground">{order.date}</span>
                <span className="font-bold">{order.amount}</span>
              </div>
              <p className="text-xs text-muted-foreground">{order.items} items</p>
            </button>
          ))}
        </div>
      )}

      {/* Order Details View */}
      {currentSection === "orders" && selectedOrder && (
        <div className="p-4 space-y-4">
          {SAMPLE_ORDERS.find((o) => o.id === selectedOrder) && (
            <>
              {(() => {
                const order = SAMPLE_ORDERS.find((o) => o.id === selectedOrder)!
                return (
                  <>
                    <div className="bg-secondary rounded-lg p-4 space-y-3">
                      <div className="flex items-center justify-between">
                        <span className="text-muted-foreground text-sm">Order Number</span>
                        <span className="font-semibold">{order.id}</span>
                      </div>
                      <div className="flex items-center justify-between">
                        <span className="text-muted-foreground text-sm">Status</span>
                        <span
                          className={`text-xs font-medium px-2 py-1 rounded-full ${
                            order.status === "Delivered" ? "bg-success/20 text-success" : "bg-accent/20 text-accent"
                          }`}
                        >
                          {order.status}
                        </span>
                      </div>
                      <div className="flex items-center justify-between">
                        <span className="text-muted-foreground text-sm">Order Date</span>
                        <span className="font-semibold">{order.date}</span>
                      </div>
                      <div className="flex items-center justify-between">
                        <span className="text-muted-foreground text-sm">Estimated Delivery</span>
                        <span className="font-semibold">{order.estimatedDelivery}</span>
                      </div>
                      <div className="flex items-center justify-between">
                        <span className="text-muted-foreground text-sm">Tracking Number</span>
                        <span className="font-semibold text-xs">{order.trackingNumber}</span>
                      </div>
                    </div>

                    <div>
                      <h4 className="font-semibold mb-3">Order Items</h4>
                      <div className="space-y-2">
                        {order.details.map((item, i) => (
                          <div key={i} className="flex items-center justify-between p-3 bg-secondary rounded-lg">
                            <div>
                              <p className="text-sm font-medium">{item.name}</p>
                              <p className="text-xs text-muted-foreground">Qty: {item.qty}</p>
                            </div>
                            <span className="font-semibold">{item.price}</span>
                          </div>
                        ))}
                      </div>
                    </div>

                    <div className="bg-secondary rounded-lg p-4 space-y-2">
                      <div className="flex items-center justify-between text-sm">
                        <span className="text-muted-foreground">Subtotal</span>
                        <span className="font-medium">{order.amount}</span>
                      </div>
                      <div className="flex items-center justify-between text-sm">
                        <span className="text-muted-foreground">Shipping</span>
                        <span className="font-medium">FREE</span>
                      </div>
                      <div className="border-t border-border pt-2 flex items-center justify-between">
                        <span className="font-semibold">Total</span>
                        <span className="font-bold text-primary">{order.amount}</span>
                      </div>
                    </div>

                    <button className="w-full bg-primary text-primary-foreground py-3 rounded-lg font-semibold hover:bg-primary/90 transition">
                      Track Order
                    </button>
                  </>
                )
              })()}
            </>
          )}
        </div>
      )}

      {/* Addresses View */}
      {currentSection === "addresses" && (
        <div className="p-4 space-y-3">
          {SAMPLE_ADDRESSES.map((address) => (
            <div key={address.id} className="p-4 bg-card border border-border rounded-lg">
              <div className="flex items-start justify-between mb-2">
                <div>
                  <div className="flex items-center gap-2">
                    <span className="font-semibold">{address.type}</span>
                    {address.isDefault && (
                      <span className="text-xs bg-primary text-primary-foreground px-2 py-0.5 rounded">Default</span>
                    )}
                  </div>
                  <p className="text-sm text-muted-foreground mt-1">{address.name}</p>
                </div>
                <button className="p-2 hover:bg-muted rounded-lg transition">
                  <Edit2 size={16} className="text-primary" />
                </button>
              </div>
              <p className="text-sm text-muted-foreground">{address.address}</p>
              <p className="text-sm text-muted-foreground">
                {address.city}, {address.state} {address.zipCode}
              </p>
            </div>
          ))}
          <button className="w-full flex items-center justify-center gap-2 py-3 border-2 border-dashed border-border rounded-lg text-primary font-medium hover:bg-muted transition">
            <Plus size={20} />
            Add New Address
          </button>
        </div>
      )}

      {/* Payment Methods View */}
      {currentSection === "payments" && (
        <div className="p-4 space-y-3">
          {SAMPLE_PAYMENTS.map((payment) => (
            <div key={payment.id} className="p-4 bg-card border border-border rounded-lg">
              <div className="flex items-start justify-between mb-2">
                <div>
                  <div className="flex items-center gap-2">
                    <span className="font-semibold">{payment.type}</span>
                    {payment.isDefault && (
                      <span className="text-xs bg-primary text-primary-foreground px-2 py-0.5 rounded">Default</span>
                    )}
                  </div>
                  <p className="text-sm text-muted-foreground mt-1">•••• {payment.last4}</p>
                </div>
                <button className="p-2 hover:bg-muted rounded-lg transition">
                  <Trash2 size={16} className="text-destructive" />
                </button>
              </div>
              <p className="text-xs text-muted-foreground">Expires {payment.expiry}</p>
            </div>
          ))}
          <button className="w-full flex items-center justify-center gap-2 py-3 border-2 border-dashed border-border rounded-lg text-primary font-medium hover:bg-muted transition">
            <Plus size={20} />
            Add Payment Method
          </button>
        </div>
      )}

      {/* Notifications View */}
      {currentSection === "notifications" && (
        <div className="p-4 space-y-4">
          <div className="space-y-3">
            {[
              { label: "Order Updates", description: "Get notified about your order status" },
              { label: "Promotional Emails", description: "Receive special offers and deals" },
              { label: "Product Recommendations", description: "Get personalized product suggestions" },
              { label: "Push Notifications", description: "Receive push notifications on your device" },
            ].map((notif, i) => (
              <div key={i} className="flex items-center justify-between p-4 bg-card border border-border rounded-lg">
                <div>
                  <p className="font-medium text-sm">{notif.label}</p>
                  <p className="text-xs text-muted-foreground">{notif.description}</p>
                </div>
                <input type="checkbox" defaultChecked className="w-5 h-5 rounded" />
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Settings View */}
      {currentSection === "settings" && (
        <div className="p-4 space-y-3">
          {[
            { label: "Change Password", description: "Update your account password" },
            { label: "Privacy Settings", description: "Manage your privacy preferences" },
            { label: "Account Preferences", description: "Customize your account settings" },
            { label: "Delete Account", description: "Permanently delete your account", danger: true },
          ].map((setting, i) => (
            <button
              key={i}
              className={`w-full flex items-center justify-between p-4 rounded-lg border transition ${
                setting.danger
                  ? "bg-destructive/10 border-destructive/20 hover:bg-destructive/20"
                  : "bg-card border-border hover:bg-muted"
              }`}
            >
              <div className="text-left">
                <p className={`font-medium text-sm ${setting.danger ? "text-destructive" : ""}`}>{setting.label}</p>
                <p className="text-xs text-muted-foreground">{setting.description}</p>
              </div>
              <ChevronRight size={20} className={setting.danger ? "text-destructive" : "text-muted-foreground"} />
            </button>
          ))}
        </div>
      )}
    </div>
  )
}
