<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Admin'e Mesaj Gönder</h3>
    
    <form wire:submit.prevent="send">
        <textarea 
            wire:model="mesaj"
            rows="3"
            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
            placeholder="Mesajınızı yazın..."
        ></textarea>
        
        <button 
            type="submit"
            wire:target="send"
            class="mt-2 w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-md transition-colors"
        >
            Gönder
        </button>
    </form>
</div>