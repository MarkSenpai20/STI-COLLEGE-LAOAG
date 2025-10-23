"use client"

import { Heart, Star } from "lucide-react"
import { useState } from "react"

interface ProductCardProps {
  product: any
  onSelect: () => void
}

export default function ProductCard({ product, onSelect }: ProductCardProps) {
  const [isFavorite, setIsFavorite] = useState(false)

  const discount = Math.round(((product.originalPrice - product.price) / product.originalPrice) * 100)

  return (
    <div
      onClick={onSelect}
      className="bg-card border border-border rounded-lg overflow-hidden hover:shadow-lg transition cursor-pointer group"
    >
      {/* Image Container */}
      <div className="relative bg-muted overflow-hidden h-40 sm:h-48">
        <img
          src={product.image || "/placeholder.svg"}
          alt={product.name}
          className="w-full h-full object-cover group-hover:scale-105 transition"
        />

        {/* Discount Badge */}
        {discount > 0 && (
          <div className="absolute top-2 right-2 bg-accent text-accent-foreground px-2 py-1 rounded-lg text-xs font-bold">
            -{discount}%
          </div>
        )}

        {/* Favorite Button */}
        <button
          onClick={(e) => {
            e.stopPropagation()
            setIsFavorite(!isFavorite)
          }}
          className="absolute top-2 left-2 p-2 bg-white/90 rounded-full hover:bg-white transition"
        >
          <Heart size={18} className={isFavorite ? "fill-destructive text-destructive" : "text-muted-foreground"} />
        </button>

        {/* Stock Badge */}
        {!product.inStock && (
          <div className="absolute inset-0 bg-black/50 flex items-center justify-center">
            <span className="text-white font-bold">Out of Stock</span>
          </div>
        )}
      </div>

      {/* Content */}
      <div className="p-3">
        <h3 className="font-semibold text-sm line-clamp-2 mb-2 group-hover:text-primary transition">{product.name}</h3>

        {/* Rating */}
        <div className="flex items-center gap-1 mb-2">
          <div className="flex gap-0.5">
            {[...Array(5)].map((_, i) => (
              <Star
                key={i}
                size={12}
                className={i < Math.floor(product.rating) ? "fill-accent text-accent" : "text-muted-foreground"}
              />
            ))}
          </div>
          <span className="text-xs text-muted-foreground">({product.reviews})</span>
        </div>

        {/* Price */}
        <div className="flex items-baseline gap-2">
          <span className="text-lg font-bold text-primary">${product.price}</span>
          <span className="text-xs text-muted-foreground line-through">${product.originalPrice}</span>
        </div>
      </div>
    </div>
  )
}
