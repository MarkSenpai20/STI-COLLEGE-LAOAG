"use client"

import { useState } from "react"
import { ChevronLeft, Heart, Share2, Star, Truck, Shield, RotateCcw, MessageCircle } from "lucide-react"

interface ProductDetailsPageProps {
  product: any
  onAddToCart: () => void
  onBack: () => void
}

const SAMPLE_REVIEWS = [
  {
    id: 1,
    author: "John Doe",
    rating: 5,
    date: "2 weeks ago",
    verified: true,
    title: "Excellent product!",
    content: "Great product! Exactly as described. Highly recommend!",
    helpful: 24,
  },
  {
    id: 2,
    author: "Sarah Smith",
    rating: 5,
    date: "1 month ago",
    verified: true,
    title: "Best purchase ever",
    content: "Amazing quality and fast shipping. Very satisfied with my purchase.",
    helpful: 18,
  },
  {
    id: 3,
    author: "Mike Johnson",
    rating: 4,
    date: "1 month ago",
    verified: true,
    title: "Good value for money",
    content: "Good product overall. Works as expected. Minor packaging issue but product is perfect.",
    helpful: 12,
  },
  {
    id: 4,
    author: "Emma Wilson",
    rating: 5,
    date: "2 months ago",
    verified: true,
    title: "Exceeded expectations",
    content: "Exceeded my expectations! The quality is outstanding and delivery was quick.",
    helpful: 31,
  },
  {
    id: 5,
    author: "David Brown",
    rating: 4,
    date: "2 months ago",
    verified: true,
    title: "Very good",
    content: "Very good product. Arrived on time. Would definitely buy again.",
    helpful: 15,
  },
]

export default function ProductDetailsPage({ product, onAddToCart, onBack }: ProductDetailsPageProps) {
  const [quantity, setQuantity] = useState(1)
  const [isFavorite, setIsFavorite] = useState(false)
  const [selectedImage, setSelectedImage] = useState(0)
  const [reviewFilter, setReviewFilter] = useState<number | null>(null)
  const [showWriteReview, setShowWriteReview] = useState(false)
  const [reviewText, setReviewText] = useState("")
  const [reviewRating, setReviewRating] = useState(5)

  if (!product) return null

  const discount = Math.round(((product.originalPrice - product.price) / product.originalPrice) * 100)

  const filteredReviews = reviewFilter ? SAMPLE_REVIEWS.filter((r) => r.rating === reviewFilter) : SAMPLE_REVIEWS

  const averageRating = (SAMPLE_REVIEWS.reduce((sum, r) => sum + r.rating, 0) / SAMPLE_REVIEWS.length).toFixed(1)

  const ratingDistribution = [5, 4, 3, 2, 1].map((rating) => ({
    rating,
    count: SAMPLE_REVIEWS.filter((r) => r.rating === rating).length,
  }))

  const handleSubmitReview = () => {
    if (reviewText.trim()) {
      setReviewText("")
      setReviewRating(5)
      setShowWriteReview(false)
      // In a real app, this would send to backend
    }
  }

  return (
    <div className="pb-24">
      {/* Header */}
      <div className="sticky top-0 z-30 bg-card border-b border-border flex items-center justify-between px-4 py-3">
        <button onClick={onBack} className="p-2 hover:bg-muted rounded-lg transition">
          <ChevronLeft size={24} />
        </button>
        <h2 className="text-lg font-semibold flex-1 text-center">Product Details</h2>
        <button className="p-2 hover:bg-muted rounded-lg transition">
          <Share2 size={24} />
        </button>
      </div>

      {/* Product Images */}
      <div className="bg-muted p-4">
        <div className="bg-white rounded-lg overflow-hidden mb-3">
          <img src={product.image || "/placeholder.svg"} alt={product.name} className="w-full h-64 object-cover" />
        </div>
        <div className="flex gap-2">
          {[0, 1, 2].map((i) => (
            <button
              key={i}
              onClick={() => setSelectedImage(i)}
              className={`w-16 h-16 rounded-lg overflow-hidden border-2 transition ${
                selectedImage === i ? "border-primary" : "border-border"
              }`}
            >
              <img
                src={product.image || "/placeholder.svg"}
                alt={`View ${i + 1}`}
                className="w-full h-full object-cover"
              />
            </button>
          ))}
        </div>
      </div>

      {/* Product Info */}
      <div className="p-4 space-y-4">
        {/* Title and Rating */}
        <div>
          <h1 className="text-2xl font-bold mb-2">{product.name}</h1>
          <div className="flex items-center gap-2">
            <div className="flex items-center gap-1">
              {[...Array(5)].map((_, i) => (
                <Star
                  key={i}
                  size={16}
                  className={i < Math.floor(product.rating) ? "fill-accent text-accent" : "text-muted-foreground"}
                />
              ))}
            </div>
            <span className="text-sm font-medium">{product.rating}</span>
            <span className="text-sm text-muted-foreground">({product.reviews} reviews)</span>
          </div>
        </div>

        {/* Price */}
        <div className="bg-muted p-3 rounded-lg">
          <div className="flex items-baseline gap-2 mb-1">
            <span className="text-3xl font-bold text-primary">${product.price}</span>
            <span className="text-lg text-muted-foreground line-through">${product.originalPrice}</span>
            <span className="bg-accent text-accent-foreground px-2 py-1 rounded text-sm font-bold">
              {discount}% OFF
            </span>
          </div>
          <p className="text-sm text-success font-medium">Limited time offer</p>
        </div>

        {/* Stock Status */}
        <div className="flex items-center gap-2">
          <div className="w-3 h-3 bg-success rounded-full"></div>
          <span className="text-sm font-medium">In Stock - Only 5 left!</span>
        </div>

        {/* Features */}
        <div className="space-y-2">
          <div className="flex items-start gap-3">
            <Truck className="text-primary mt-1" size={20} />
            <div>
              <p className="font-medium text-sm">Free Shipping</p>
              <p className="text-xs text-muted-foreground">On orders over $50</p>
            </div>
          </div>
          <div className="flex items-start gap-3">
            <Shield className="text-primary mt-1" size={20} />
            <div>
              <p className="font-medium text-sm">2-Year Warranty</p>
              <p className="text-xs text-muted-foreground">Full coverage included</p>
            </div>
          </div>
          <div className="flex items-start gap-3">
            <RotateCcw className="text-primary mt-1" size={20} />
            <div>
              <p className="font-medium text-sm">30-Day Returns</p>
              <p className="text-xs text-muted-foreground">Money-back guarantee</p>
            </div>
          </div>
        </div>

        {/* Description */}
        <div>
          <h3 className="font-semibold mb-2">Description</h3>
          <p className="text-sm text-muted-foreground leading-relaxed">
            Experience premium quality with this exceptional product. Crafted with attention to detail and built to
            last, it combines functionality with elegant design. Perfect for everyday use or special occasions.
          </p>
        </div>

        {/* Specifications */}
        <div>
          <h3 className="font-semibold mb-2">Specifications</h3>
          <div className="space-y-2 text-sm">
            <div className="flex justify-between">
              <span className="text-muted-foreground">Brand</span>
              <span className="font-medium">Premium Brand</span>
            </div>
            <div className="flex justify-between">
              <span className="text-muted-foreground">Model</span>
              <span className="font-medium">PRO-2024</span>
            </div>
            <div className="flex justify-between">
              <span className="text-muted-foreground">Color</span>
              <span className="font-medium">Black</span>
            </div>
            <div className="flex justify-between">
              <span className="text-muted-foreground">Warranty</span>
              <span className="font-medium">2 Years</span>
            </div>
          </div>
        </div>

        {/* Reviews Section */}
        <div>
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-lg">Customer Reviews</h3>
            <button
              onClick={() => setShowWriteReview(!showWriteReview)}
              className="flex items-center gap-1 text-primary hover:text-primary/80 transition text-sm font-medium"
            >
              <MessageCircle size={16} />
              Write Review
            </button>
          </div>

          {/* Write Review Form */}
          {showWriteReview && (
            <div className="bg-secondary rounded-lg p-4 mb-4 space-y-3">
              <div>
                <label className="text-sm font-medium mb-2 block">Rating</label>
                <div className="flex gap-2">
                  {[1, 2, 3, 4, 5].map((star) => (
                    <button key={star} onClick={() => setReviewRating(star)} className="transition">
                      <Star
                        size={24}
                        className={star <= reviewRating ? "fill-accent text-accent" : "text-muted-foreground"}
                      />
                    </button>
                  ))}
                </div>
              </div>
              <div>
                <label className="text-sm font-medium mb-2 block">Your Review</label>
                <textarea
                  value={reviewText}
                  onChange={(e) => setReviewText(e.target.value)}
                  placeholder="Share your experience with this product..."
                  className="w-full p-3 bg-background border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary resize-none"
                  rows={4}
                />
              </div>
              <div className="flex gap-2">
                <button
                  onClick={handleSubmitReview}
                  disabled={!reviewText.trim()}
                  className="flex-1 bg-primary text-primary-foreground py-2 rounded-lg font-medium hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Submit Review
                </button>
                <button
                  onClick={() => setShowWriteReview(false)}
                  className="flex-1 bg-muted text-foreground py-2 rounded-lg font-medium hover:bg-muted/80 transition"
                >
                  Cancel
                </button>
              </div>
            </div>
          )}

          {/* Rating Summary */}
          <div className="bg-secondary rounded-lg p-4 mb-4">
            <div className="flex items-start gap-4">
              <div className="text-center">
                <div className="text-3xl font-bold text-primary">{averageRating}</div>
                <div className="flex gap-1 justify-center mt-1">
                  {[...Array(5)].map((_, i) => (
                    <Star
                      key={i}
                      size={14}
                      className={
                        i < Math.floor(Number.parseFloat(averageRating))
                          ? "fill-accent text-accent"
                          : "text-muted-foreground"
                      }
                    />
                  ))}
                </div>
                <p className="text-xs text-muted-foreground mt-1">{SAMPLE_REVIEWS.length} reviews</p>
              </div>
              <div className="flex-1 space-y-2">
                {ratingDistribution.map(({ rating, count }) => (
                  <div key={rating} className="flex items-center gap-2">
                    <button
                      onClick={() => setReviewFilter(reviewFilter === rating ? null : rating)}
                      className="text-xs text-muted-foreground hover:text-foreground transition"
                    >
                      {rating}★
                    </button>
                    <div className="flex-1 h-2 bg-muted rounded-full overflow-hidden">
                      <div
                        className="h-full bg-accent transition-all"
                        style={{
                          width: `${(count / SAMPLE_REVIEWS.length) * 100}%`,
                        }}
                      />
                    </div>
                    <span className="text-xs text-muted-foreground w-6 text-right">{count}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Filter Buttons */}
          <div className="flex gap-2 mb-4 overflow-x-auto pb-2">
            <button
              onClick={() => setReviewFilter(null)}
              className={`px-3 py-1 rounded-full text-sm font-medium whitespace-nowrap transition ${
                reviewFilter === null
                  ? "bg-primary text-primary-foreground"
                  : "bg-secondary text-foreground hover:bg-secondary/80"
              }`}
            >
              All
            </button>
            {[5, 4, 3, 2, 1].map((rating) => (
              <button
                key={rating}
                onClick={() => setReviewFilter(reviewFilter === rating ? null : rating)}
                className={`px-3 py-1 rounded-full text-sm font-medium whitespace-nowrap transition ${
                  reviewFilter === rating
                    ? "bg-primary text-primary-foreground"
                    : "bg-secondary text-foreground hover:bg-secondary/80"
                }`}
              >
                {rating}★
              </button>
            ))}
          </div>

          {/* Reviews List */}
          <div className="space-y-3">
            {filteredReviews.length > 0 ? (
              filteredReviews.map((review) => (
                <div key={review.id} className="border border-border rounded-lg p-3">
                  <div className="flex items-start justify-between mb-2">
                    <div>
                      <div className="flex items-center gap-2">
                        <span className="font-medium text-sm">{review.author}</span>
                        {review.verified && (
                          <span className="text-xs bg-success/20 text-success px-2 py-0.5 rounded">Verified</span>
                        )}
                      </div>
                      <p className="text-xs text-muted-foreground">{review.date}</p>
                    </div>
                    <div className="flex gap-0.5">
                      {[...Array(5)].map((_, j) => (
                        <Star
                          key={j}
                          size={14}
                          className={j < review.rating ? "fill-accent text-accent" : "text-muted-foreground"}
                        />
                      ))}
                    </div>
                  </div>
                  <h4 className="font-medium text-sm mb-1">{review.title}</h4>
                  <p className="text-sm text-muted-foreground mb-2">{review.content}</p>
                  <button className="text-xs text-muted-foreground hover:text-foreground transition">
                    Helpful ({review.helpful})
                  </button>
                </div>
              ))
            ) : (
              <p className="text-center text-muted-foreground text-sm py-4">No reviews found for this rating.</p>
            )}
          </div>
        </div>
      </div>

      {/* Bottom Action Bar */}
      <div className="fixed bottom-0 left-0 right-0 bg-card border-t border-border p-4 space-y-3">
        <div className="flex items-center gap-3">
          <button
            onClick={() => setIsFavorite(!isFavorite)}
            className={`flex-1 py-3 rounded-lg border-2 transition flex items-center justify-center gap-2 ${
              isFavorite ? "bg-accent/10 border-accent text-accent" : "border-border hover:border-primary"
            }`}
          >
            <Heart size={20} fill={isFavorite ? "currentColor" : "none"} />
            <span className="font-medium">Wishlist</span>
          </button>

          <div className="flex items-center gap-2 bg-muted rounded-lg">
            <button
              onClick={() => setQuantity(Math.max(1, quantity - 1))}
              className="px-3 py-2 hover:bg-border rounded transition"
            >
              −
            </button>
            <span className="px-3 font-medium">{quantity}</span>
            <button onClick={() => setQuantity(quantity + 1)} className="px-3 py-2 hover:bg-border rounded transition">
              +
            </button>
          </div>
        </div>

        <button
          onClick={onAddToCart}
          className="w-full bg-primary text-primary-foreground py-3 rounded-lg font-semibold hover:bg-primary/90 transition"
        >
          Add to Cart
        </button>
      </div>
    </div>
  )
}
