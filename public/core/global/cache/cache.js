/**
 * Cache manager class for handling data caching with size limits, expiration, memory limits, and statistics
 */
export class CacheManager {
    constructor(options = {}) {
        this.maxEntries = options.maxEntries || 100;
        this.maxAge = options.maxAge || 10 * 60 * 1000; // 10 minutes default
        this.maxMemoryUsage = options.maxMemoryUsage || 50 * 1024 * 1024; // 50MB default
        this.currentMemoryUsage = 0;
        this.cache = new Map();
        this.stats = {
            hits: 0,
            misses: 0,
            evictions: 0,
            memoryEvictions: 0
        };
    }

    /**
     * Set a cache entry with optional custom expiration
     * @param {string} key - Cache key
     * @param {*} data - Data to cache
     * @param {number} [customMaxAge] - Optional custom expiration time in ms
     */
    set(key, data, customMaxAge) {
        const estimatedSize = this._getEstimatedSize(data);

        // Check if adding this item would exceed memory limit
        while (this.currentMemoryUsage + estimatedSize > this.maxMemoryUsage && this.cache.size > 0) {
            this._evictOldest();
            this.stats.memoryEvictions++;
        }

        // Manage cache size
        if (this.cache.size >= this.maxEntries) {
            this._evictOldest();
        }

        this.cache.set(key, {
            data,
            timestamp: Date.now(),
            maxAge: customMaxAge || this.maxAge,
            accessCount: 0,
            size: estimatedSize
        });

        this.currentMemoryUsage += estimatedSize;
    }

    /**
     * Get a cache entry if it exists and is valid
     * @param {string} key - Cache key
     * @returns {*|null} Cached data or null if not found/expired
     */
    get(key) {
        const entry = this.cache.get(key);

        if (!entry) {
            this.stats.misses++;
            return null;
        }

        // Check expiration
        if (this._isExpired(entry)) {
            this.delete(key);
            this.stats.misses++;
            return null;
        }

        entry.accessCount++;
        this.stats.hits++;
        return entry.data;
    }

    /**
     * Delete a specific cache entry
     * @param {string} key - Cache key to delete
     */
    delete(key) {
        const entry = this.cache.get(key);
        if (entry) {
            this.currentMemoryUsage -= entry.size;
        }
        this.cache.delete(key);
    }

    /**
     * Clear all cache entries
     */
    clear() {
        this.cache.clear();
        this.currentMemoryUsage = 0;
        this.resetStats();
    }

    /**
     * Get cache statistics
     * @returns {Object} Cache statistics
     */
    getStats() {
        return {
            ...this.stats,
            size: this.cache.size,
            memoryUsage: this.currentMemoryUsage,
            memoryUsagePercentage: (this.currentMemoryUsage / this.maxMemoryUsage) * 100,
            hitRate: this._calculateHitRate()
        };
    }

    /**
     * Reset cache statistics
     */
    resetStats() {
        this.stats = {
            hits: 0,
            misses: 0,
            evictions: 0,
            memoryEvictions: 0
        };
    }

    /**
     * Check if a cache entry has expired
     * @private
     */
    _isExpired(entry) {
        return Date.now() - entry.timestamp > entry.maxAge;
    }

    /**
     * Evict the oldest cache entry
     * @private
     */
    _evictOldest() {
        const oldestKey = Array.from(this.cache.entries())
            .reduce((oldest, current) => {
                if (!oldest) return current;
                return oldest[1].timestamp < current[1].timestamp ? oldest : current;
            })[0];

        this.delete(oldestKey);
        this.stats.evictions++;
    }

    /**
     * Calculate cache hit rate
     * @private
     */
    _calculateHitRate() {
        const total = this.stats.hits + this.stats.misses;
        return total === 0 ? 0 : (this.stats.hits / total) * 100;
    }

    /**
     * Calculate estimated size of data in bytes
     * @private
     */
    _getEstimatedSize(data) {
        if (typeof data === 'string') {
            return new TextEncoder().encode(data).length;
        } else if (typeof data === 'number') {
            return 8;
        } else if (typeof data === 'boolean') {
            return 4;
        } else if (data === null || data === undefined) {
            return 0;
        } else if (Array.isArray(data) || typeof data === 'object') {
            return new TextEncoder().encode(JSON.stringify(data)).length;
        }
        return 0;
    }

    /**
     * Get all keys in cache
     * @returns {Array} Array of cache keys
     */
    keys() {
        return Array.from(this.cache.keys());
    }

    /**
     * Check if key exists in cache and is valid
     * @param {string} key - Cache key
     * @returns {boolean} Whether key exists and is valid
     */
    has(key) {
        const entry = this.cache.get(key);
        if (!entry) return false;
        if (this._isExpired(entry)) {
            this.delete(key);
            return false;
        }
        return true;
    }
}
