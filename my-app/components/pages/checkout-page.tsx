"use client"

import type React from "react"

import { useState } from "react"
import { ChevronLeft, MapPin, CreditCard, CheckCircle } from "lucide-react"

interface CheckoutPageProps {
  onBack: () => void
  onOrderComplete: () => void
}

const PAYMENT_METHODS = [
  { id: "card", name: "Credit/Debit Card", icon: "💳" },
  { id: "paypal", name: "PayPal", icon: "🅿️" },
  { id: "apple", name: "Apple Pay", icon: "🍎" },
]

export default function CheckoutPage({ onBack, onOrderComplete }: CheckoutPageProps) {
  const [step, setStep] = useState<"shipping" | "payment" | "review" | "confirmation">("shipping")
  const [formData, setFormData] = useState({
    firstName: "",
    lastName: "",
    email: "",
    phone: "",
    address: "",
    city: "",
    state: "",
    zipCode: "",
    country: "",
  })
  const [selectedPayment, setSelectedPayment] = useState("card")
  const [cardData, setCardData] = useState({
    cardNumber: "",
    cardName: "",
    expiryDate: "",
    cvv: "",
  })
  const [isProcessing, setIsProcessing] = useState(false)

  const subtotal = 189.97
  const shipping = 0
  const tax = 18.997
  const total = subtotal + shipping + tax

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target
    setFormData((prev) => ({ ...prev, [name]: value }))
  }

  const handleCardInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target
    let formattedValue = value

    if (name === "cardNumber") {
      formattedValue = value
        .replace(/\s/g, "")
        .replace(/(\d{4})/g, "$1 ")
        .trim()
    } else if (name === "expiryDate") {
      formattedValue = value.replace(/\D/g, "").replace(/(\d{2})(\d{0,2})/, "$1/$2")
    } else if (name === "cvv") {
      formattedValue = value.replace(/\D/g, "").slice(0, 3)
    }

    setCardData((prev) => ({ ...prev, [name]: formattedValue }))
  }

  const isShippingComplete =
    formData.firstName &&
    formData.lastName &&
    formData.email &&
    formData.phone &&
    formData.address &&
    formData.city &&
    formData.state &&
    formData.zipCode &&
    formData.country

  const isPaymentComplete =
    selectedPayment === "card" ? cardData.cardNumber && cardData.cardName && cardData.expiryDate && cardData.cvv : true

  const handlePlaceOrder = async () => {
    setIsProcessing(true)
    // Simulate payment processing
    await new Promise((resolve) => setTimeout(resolve, 2000))
    setIsProcessing(false)
    setStep("confirmation")
  }

  return (
    <div className="pb-24">
      {/* Header */}
      <div className="sticky top-0 z-30 bg-card border-b border-border flex items-center gap-3 px-4 py-3">
        <button
          onClick={onBack}
          disabled={isProcessing}
          className="p-2 hover:bg-muted rounded-lg transition disabled:opacity-50"
        >
          <ChevronLeft size={24} />
        </button>
        <h2 className="text-lg font-semibold flex-1">Checkout</h2>
        <span className="text-xs font-medium text-muted-foreground">
          Step {step === "shipping" ? 1 : step === "payment" ? 2 : step === "review" ? 3 : 4} of 4
        </span>
      </div>

      {/* Progress Indicator */}
      <div className="px-4 py-4 flex gap-2">
        {["shipping", "payment", "review", "confirmation"].map((s, i) => (
          <div key={s} className="flex items-center flex-1">
            <div
              className={`w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition ${
                step === s
                  ? "bg-primary text-primary-foreground"
                  : ["shipping", "payment", "review"].includes(s) &&
                      ["shipping", "payment", "review", "confirmation"].indexOf(step) >
                        ["shipping", "payment", "review", "confirmation"].indexOf(s)
                    ? "bg-success text-success"
                    : "bg-muted text-muted-foreground"
              }`}
            >
              {["shipping", "payment", "review", "confirmation"].indexOf(step) >
              ["shipping", "payment", "review", "confirmation"].indexOf(s) ? (
                <CheckCircle size={20} />
              ) : (
                i + 1
              )}
            </div>
            {i < 3 && <div className="flex-1 h-1 bg-muted mx-1" />}
          </div>
        ))}
      </div>

      {/* Content */}
      <div className="px-4 pb-4">
        {/* Shipping Address */}
        {step === "shipping" && (
          <div className="space-y-4">
            <div className="flex items-center gap-2 mb-4">
              <MapPin size={20} className="text-primary" />
              <h3 className="text-lg font-semibold">Shipping Address</h3>
            </div>

            <div className="grid grid-cols-2 gap-3">
              <input
                type="text"
                name="firstName"
                placeholder="First Name"
                value={formData.firstName}
                onChange={handleInputChange}
                className="col-span-1 px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
              />
              <input
                type="text"
                name="lastName"
                placeholder="Last Name"
                value={formData.lastName}
                onChange={handleInputChange}
                className="col-span-1 px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
              />
            </div>

            <input
              type="email"
              name="email"
              placeholder="Email Address"
              value={formData.email}
              onChange={handleInputChange}
              className="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
            />

            <input
              type="tel"
              name="phone"
              placeholder="Phone Number"
              value={formData.phone}
              onChange={handleInputChange}
              className="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
            />

            <input
              type="text"
              name="address"
              placeholder="Street Address"
              value={formData.address}
              onChange={handleInputChange}
              className="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
            />

            <div className="grid grid-cols-2 gap-3">
              <input
                type="text"
                name="city"
                placeholder="City"
                value={formData.city}
                onChange={handleInputChange}
                className="col-span-1 px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
              />
              <input
                type="text"
                name="state"
                placeholder="State"
                value={formData.state}
                onChange={handleInputChange}
                className="col-span-1 px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
              />
            </div>

            <div className="grid grid-cols-2 gap-3">
              <input
                type="text"
                name="zipCode"
                placeholder="ZIP Code"
                value={formData.zipCode}
                onChange={handleInputChange}
                className="col-span-1 px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
              />
              <select
                name="country"
                value={formData.country}
                onChange={handleInputChange}
                className="col-span-1 px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
              >
                <option value="">Select Country</option>
                <option value="US">United States</option>
                <option value="CA">Canada</option>
                <option value="UK">United Kingdom</option>
                <option value="AU">Australia</option>
              </select>
            </div>

            <button
              onClick={() => setStep("payment")}
              disabled={!isShippingComplete}
              className="w-full bg-primary text-primary-foreground py-3 rounded-lg font-semibold hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed mt-4"
            >
              Continue to Payment
            </button>
          </div>
        )}

        {/* Payment Method */}
        {step === "payment" && (
          <div className="space-y-4">
            <div className="flex items-center gap-2 mb-4">
              <CreditCard size={20} className="text-primary" />
              <h3 className="text-lg font-semibold">Payment Method</h3>
            </div>

            <div className="space-y-2 mb-4">
              {PAYMENT_METHODS.map((method) => (
                <button
                  key={method.id}
                  onClick={() => setSelectedPayment(method.id)}
                  className={`w-full p-3 rounded-lg border-2 transition flex items-center gap-3 ${
                    selectedPayment === method.id
                      ? "border-primary bg-primary/5"
                      : "border-border hover:border-primary/50"
                  }`}
                >
                  <span className="text-2xl">{method.icon}</span>
                  <span className="font-medium">{method.name}</span>
                </button>
              ))}
            </div>

            {selectedPayment === "card" && (
              <div className="space-y-3 bg-secondary p-4 rounded-lg">
                <input
                  type="text"
                  name="cardNumber"
                  placeholder="Card Number"
                  value={cardData.cardNumber}
                  onChange={handleCardInputChange}
                  maxLength="19"
                  className="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                />
                <input
                  type="text"
                  name="cardName"
                  placeholder="Cardholder Name"
                  value={cardData.cardName}
                  onChange={handleCardInputChange}
                  className="w-full px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                />
                <div className="grid grid-cols-2 gap-3">
                  <input
                    type="text"
                    name="expiryDate"
                    placeholder="MM/YY"
                    value={cardData.expiryDate}
                    onChange={handleCardInputChange}
                    maxLength="5"
                    className="col-span-1 px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                  />
                  <input
                    type="text"
                    name="cvv"
                    placeholder="CVV"
                    value={cardData.cvv}
                    onChange={handleCardInputChange}
                    maxLength="3"
                    className="col-span-1 px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                  />
                </div>
              </div>
            )}

            <div className="flex gap-3 mt-4">
              <button
                onClick={() => setStep("shipping")}
                className="flex-1 bg-muted text-foreground py-3 rounded-lg font-semibold hover:bg-muted/80 transition"
              >
                Back
              </button>
              <button
                onClick={() => setStep("review")}
                disabled={!isPaymentComplete}
                className="flex-1 bg-primary text-primary-foreground py-3 rounded-lg font-semibold hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Review Order
              </button>
            </div>
          </div>
        )}

        {/* Order Review */}
        {step === "review" && (
          <div className="space-y-4">
            <h3 className="text-lg font-semibold mb-4">Order Review</h3>

            <div className="bg-secondary rounded-lg p-4 space-y-3">
              <div>
                <p className="text-xs text-muted-foreground mb-1">Shipping To</p>
                <p className="font-medium text-sm">
                  {formData.firstName} {formData.lastName}
                </p>
                <p className="text-sm text-muted-foreground">
                  {formData.address}, {formData.city}, {formData.state} {formData.zipCode}
                </p>
              </div>

              <div className="border-t border-border pt-3">
                <p className="text-xs text-muted-foreground mb-1">Payment Method</p>
                <p className="font-medium text-sm">{PAYMENT_METHODS.find((m) => m.id === selectedPayment)?.name}</p>
              </div>
            </div>

            <div className="bg-secondary rounded-lg p-4 space-y-2">
              <h4 className="font-semibold text-sm mb-3">Order Summary</h4>
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Subtotal</span>
                <span>${subtotal.toFixed(2)}</span>
              </div>
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Shipping</span>
                <span>{shipping === 0 ? "FREE" : `$${shipping.toFixed(2)}`}</span>
              </div>
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Tax</span>
                <span>${tax.toFixed(2)}</span>
              </div>
              <div className="border-t border-border pt-2 flex justify-between font-semibold">
                <span>Total</span>
                <span className="text-primary">${total.toFixed(2)}</span>
              </div>
            </div>

            <div className="flex gap-3">
              <button
                onClick={() => setStep("payment")}
                className="flex-1 bg-muted text-foreground py-3 rounded-lg font-semibold hover:bg-muted/80 transition"
              >
                Back
              </button>
              <button
                onClick={handlePlaceOrder}
                disabled={isProcessing}
                className="flex-1 bg-accent text-accent-foreground py-3 rounded-lg font-semibold hover:bg-accent/90 transition disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {isProcessing ? "Processing..." : "Place Order"}
              </button>
            </div>
          </div>
        )}

        {/* Order Confirmation */}
        {step === "confirmation" && (
          <div className="flex flex-col items-center justify-center py-12 text-center space-y-4">
            <div className="text-6xl">✓</div>
            <h3 className="text-2xl font-bold">Order Confirmed!</h3>
            <p className="text-muted-foreground max-w-xs">
              Thank you for your purchase. Your order has been placed successfully.
            </p>

            <div className="bg-secondary rounded-lg p-4 w-full text-left space-y-2 my-4">
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Order Number</span>
                <span className="font-medium">#ORD-2024-12345</span>
              </div>
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Total Amount</span>
                <span className="font-medium">${total.toFixed(2)}</span>
              </div>
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Estimated Delivery</span>
                <span className="font-medium">3-5 Business Days</span>
              </div>
            </div>

            <p className="text-xs text-muted-foreground">A confirmation email has been sent to {formData.email}</p>

            <button
              onClick={() => {
                onOrderComplete()
                onBack()
              }}
              className="w-full bg-primary text-primary-foreground py-3 rounded-lg font-semibold hover:bg-primary/90 transition mt-4"
            >
              Continue Shopping
            </button>
          </div>
        )}
      </div>
    </div>
  )
}
