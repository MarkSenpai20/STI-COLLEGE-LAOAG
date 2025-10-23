"use client"

import { useState } from "react"
import { Filter, Sparkles, SearchIcon } from "lucide-react"
import ProductCard from "@/components/product-card"

const CATEGORIES = [
  { id: 1, name: "Electronics", icon: "📱" },
  { id: 2, name: "Fashion", icon: "👕" },
  { id: 3, name: "Home", icon: "🏠" },
  { id: 4, name: "Sports", icon: "⚽" },
  { id: 5, name: "Books", icon: "📚" },
]

const FEATURED_PRODUCTS = [
  {
    id: 1,
    name: "Premium Wireless Headphones",
    price: 129.99,
    originalPrice: 199.99,
    rating: 4.8,
    reviews: 2543,
    image: "/premium-wireless-headphones.png",
    category: "Electronics",
    inStock: true,
  },
  {
    id: 2,
    name: "Organic Cotton T-Shirt",
    price: 34.99,
    originalPrice: 49.99,
    rating: 4.6,
    reviews: 1203,
    image: "/organic-cotton-tshirt.png",
    category: "Fashion",
    inStock: true,
  },
  {
    id: 3,
    name: "Smart Home LED Bulb",
    price: 24.99,
    originalPrice: 39.99,
    rating: 4.7,
    reviews: 892,
    image: "/smart-home-led-bulb.jpg",
    category: "Home",
    inStock: true,
  },
  {
    id: 4,
    name: "Professional Yoga Mat",
    price: 44.99,
    originalPrice: 69.99,
    rating: 4.9,
    reviews: 1567,
    image: "/professional-yoga-mat.jpg",
    category: "Sports",
    inStock: true,
  },
  {
    id: 5,
    name: "Bestseller Novel Bundle",
    price: 39.99,
    originalPrice: 59.99,
    rating: 4.5,
    reviews: 734,
    image: "/stack-of-diverse-books.jpg",
    category: "Books",
    inStock: true,
  },
  {
    id: 6,
    name: "4K Webcam",
    price: 89.99,
    originalPrice: 129.99,
    rating: 4.7,
    reviews: 456,
    image: "/4k-webcam.png",
    category: "Electronics",
    inStock: true,
  },
]

interface HomePageProps {
  onProductSelect: (product: any) => void
}

export default function HomePage({ onProductSelect }: HomePageProps) {
  const [selectedCategory, setSelectedCategory] = useState<number | null>(null)
  const [showFilters, setShowFilters] = useState(false)
  const [searchQuery, setSearchQuery] = useState("")
  const [priceRange, setPriceRange] = useState([0, 200])
  const [minRating, setMinRating] = useState(0)
  const [inStockOnly, setInStockOnly] = useState(false)
  const [sortBy, setSortBy] = useState<"relevance" | "price-low" | "price-high" | "rating">("relevance")

  const filteredProducts = FEATURED_PRODUCTS.filter((product) => {
    const matchesSearch = product.name.toLowerCase().includes(searchQuery.toLowerCase())
    const matchesCategory =
      !selectedCategory || product.category === CATEGORIES.find((c) => c.id === selectedCategory)?.name
    const matchesPrice = product.price >= priceRange[0] && product.price <= priceRange[1]
    const matchesRating = product.rating >= minRating
    const matchesStock = !inStockOnly || product.inStock

    return matchesSearch && matchesCategory && matchesPrice && matchesRating && matchesStock
  })

  if (sortBy === "price-low") {
    filteredProducts.sort((a, b) => a.price - b.price)
  } else if (sortBy === "price-high") {
    filteredProducts.sort((a, b) => b.price - a.price)
  } else if (sortBy === "rating") {
    filteredProducts.sort((a, b) => b.rating - a.rating)
  }

  const hasActiveFilters =
    selectedCategory || searchQuery || priceRange[0] > 0 || priceRange[1] < 200 || minRating > 0 || inStockOnly

  const clearFilters = () => {
    setSelectedCategory(null)
    setSearchQuery("")
    setPriceRange([0, 200])
    setMinRating(0)
    setInStockOnly(false)
    setSortBy("relevance")
  }

  return (
    <div className="pb-8">
      {/* Hero Banner */}
      <div className="bg-gradient-to-br from-primary via-primary to-accent text-primary-foreground p-8 m-4 rounded-xl shadow-lg">
        <div className="flex items-start justify-between mb-4">
          <div>
            <p className="text-sm font-medium opacity-90 mb-2">Welcome Back</p>
            <h2 className="text-3xl font-bold leading-tight">Discover Amazing Deals</h2>
          </div>
          <Sparkles size={24} className="opacity-80" />
        </div>
        <p className="text-sm opacity-90 max-w-xs">
          Explore curated collections and exclusive offers on your favorite products
        </p>
      </div>

      {/* Search Bar */}
      <div className="px-4 py-4">
        <div className="relative">
          <SearchIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground" size={18} />
          <input
            type="text"
            placeholder="Search products..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="w-full pl-10 pr-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
          />
        </div>
      </div>

      {/* Category Scroll */}
      <div className="px-4 mb-4">
        <h3 className="text-lg font-semibold mb-3 text-foreground">Shop by Category</h3>
        <div className="flex gap-3 overflow-x-auto pb-2 snap-x snap-mandatory">
          {CATEGORIES.map((category) => (
            <button
              key={category.id}
              onClick={() => setSelectedCategory(selectedCategory === category.id ? null : category.id)}
              className={`flex flex-col items-center gap-2 px-4 py-3 rounded-xl whitespace-nowrap transition flex-shrink-0 snap-center ${
                selectedCategory === category.id
                  ? "bg-primary text-primary-foreground shadow-md scale-105"
                  : "bg-secondary text-foreground hover:bg-secondary/80"
              }`}
            >
              <span className="text-2xl">{category.icon}</span>
              <span className="text-xs font-medium">{category.name}</span>
            </button>
          ))}
        </div>
      </div>

      {/* Filter and Sort Controls */}
      <div className="px-4 mb-4 flex gap-2">
        <button
          onClick={() => setShowFilters(!showFilters)}
          className={`flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition ${
            showFilters || hasActiveFilters
              ? "bg-primary text-primary-foreground"
              : "bg-secondary text-foreground hover:bg-secondary/80"
          }`}
        >
          <Filter size={18} />
          <span className="text-sm">Filters</span>
          {hasActiveFilters && <span className="ml-1 text-xs bg-accent px-2 py-0.5 rounded-full">Active</span>}
        </button>

        <select
          value={sortBy}
          onChange={(e) => setSortBy(e.target.value as any)}
          className="flex-1 px-3 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
        >
          <option value="relevance">Relevance</option>
          <option value="price-low">Price: Low to High</option>
          <option value="price-high">Price: High to Low</option>
          <option value="rating">Highest Rated</option>
        </select>
      </div>

      {/* Filter Panel */}
      {showFilters && (
        <div className="px-4 mb-4 p-4 bg-secondary rounded-lg space-y-4">
          {/* Price Range */}
          <div>
            <label className="text-sm font-medium block mb-2">Price Range</label>
            <div className="space-y-2">
              <input
                type="range"
                min="0"
                max="200"
                value={priceRange[1]}
                onChange={(e) => setPriceRange([priceRange[0], Number(e.target.value)])}
                className="w-full"
              />
              <div className="flex items-center justify-between text-sm">
                <span className="text-muted-foreground">${priceRange[0]}</span>
                <span className="font-medium">${priceRange[1]}</span>
              </div>
            </div>
          </div>

          {/* Minimum Rating */}
          <div>
            <label className="text-sm font-medium block mb-2">Minimum Rating</label>
            <div className="flex gap-2">
              {[0, 3, 4, 4.5].map((rating) => (
                <button
                  key={rating}
                  onClick={() => setMinRating(minRating === rating ? 0 : rating)}
                  className={`px-3 py-1 rounded text-sm transition ${
                    minRating === rating ? "bg-primary text-primary-foreground" : "bg-background hover:bg-border"
                  }`}
                >
                  {rating === 0 ? "All" : `${rating}★+`}
                </button>
              ))}
            </div>
          </div>

          {/* Stock Filter */}
          <div className="flex items-center gap-2">
            <input
              type="checkbox"
              id="inStock"
              checked={inStockOnly}
              onChange={(e) => setInStockOnly(e.target.checked)}
              className="w-4 h-4 rounded"
            />
            <label htmlFor="inStock" className="text-sm font-medium cursor-pointer">
              In Stock Only
            </label>
          </div>

          {/* Clear Filters */}
          {hasActiveFilters && (
            <button
              onClick={clearFilters}
              className="w-full py-2 text-sm font-medium text-primary hover:bg-background rounded transition"
            >
              Clear All Filters
            </button>
          )}
        </div>
      )}

      {/* Results Header */}
      <div className="px-4 mb-4">
        <div className="flex items-center justify-between">
          <h3 className="text-lg font-semibold text-foreground">
            {searchQuery
              ? `Search Results for "${searchQuery}"`
              : selectedCategory
                ? `${CATEGORIES.find((c) => c.id === selectedCategory)?.name} Products`
                : "Featured Products"}
          </h3>
          <span className="text-sm text-muted-foreground">{filteredProducts.length} results</span>
        </div>
      </div>

      {/* Products Grid */}
      {filteredProducts.length > 0 ? (
        <div className="px-4">
          <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            {filteredProducts.map((product) => (
              <ProductCard key={product.id} product={product} onSelect={() => onProductSelect(product)} />
            ))}
          </div>
        </div>
      ) : (
        <div className="px-4 py-12 text-center">
          <p className="text-muted-foreground mb-4">No products found matching your filters.</p>
          <button onClick={clearFilters} className="text-primary font-medium hover:underline">
            Clear filters and try again
          </button>
        </div>
      )}

      {/* Promotional Banner */}
      <div className="px-4 mt-10">
        <div className="bg-gradient-to-r from-accent/20 to-accent/10 border-2 border-accent rounded-xl p-6 text-center">
          <div className="flex justify-center mb-3">
            <span className="text-3xl">🎉</span>
          </div>
          <p className="text-xs font-semibold text-accent uppercase tracking-wide mb-2">Limited Time Offer</p>
          <p className="text-xl font-bold text-foreground mb-4">Get 20% off on your first order</p>
          <button className="bg-accent text-accent-foreground px-8 py-3 rounded-lg font-semibold hover:bg-accent/90 transition shadow-md">
            Shop Now
          </button>
        </div>
      </div>
    </div>
  )
}
