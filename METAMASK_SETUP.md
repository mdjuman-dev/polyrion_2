# MetaMask Payment Integration Setup Guide

## Required Credentials

MetaMask payment integration requires the following credentials to be configured in your `.env` file:

### 1. Blockchain Explorer API Keys

You need API keys from blockchain explorers to verify transactions. These are **FREE** to obtain:

#### **Etherscan API Key** (for Ethereum Mainnet)
- **Where to get it:** https://etherscan.io/apis
- **Steps:**
  1. Go to https://etherscan.io/register
  2. Create a free account
  3. Go to https://etherscan.io/myapikey
  4. Click "Add" to create a new API key
  5. Copy the API key
- **Free tier:** 5 calls/second, 100,000 calls/day
- **Add to .env:** `ETHERSCAN_API_KEY=your_api_key_here`

#### **BSCScan API Key** (for Binance Smart Chain)
- **Where to get it:** https://bscscan.com/apis
- **Steps:**
  1. Go to https://bscscan.com/register
  2. Create a free account
  3. Go to https://bscscan.com/myapikey
  4. Click "Add" to create a new API key
  5. Copy the API key
- **Free tier:** 5 calls/second, 100,000 calls/day
- **Add to .env:** `BSCSCAN_API_KEY=your_api_key_here` (or use `ETHERSCAN_API_KEY` if same)

#### **PolygonScan API Key** (for Polygon Network)
- **Where to get it:** https://polygonscan.com/apis
- **Steps:**
  1. Go to https://polygonscan.com/register
  2. Create a free account
  3. Go to https://polygonscan.com/myapikey
  4. Click "Add" to create a new API key
  5. Copy the API key
- **Free tier:** 5 calls/second, 100,000 calls/day
- **Add to .env:** `POLYGONSCAN_API_KEY=your_api_key_here` (or use `ETHERSCAN_API_KEY` if same)

### 2. Merchant Wallet Addresses

You need to set up wallet addresses where payments will be received. These are your **crypto wallet addresses**.

#### **How to Get Wallet Addresses:**

1. **Install MetaMask** (if not already installed)
   - Chrome: https://chrome.google.com/webstore/detail/metamask
   - Firefox: https://addons.mozilla.org/firefox/addon/ether-metamask/
   - Mobile: https://metamask.io/download/

2. **Create or Import Wallet**
   - Open MetaMask
   - Create a new wallet or import existing one
   - **IMPORTANT:** Save your seed phrase securely - this is your backup!

3. **Get Your Wallet Address**
   - Open MetaMask
   - Click on your account name at the top
   - Click "Copy address" or click the address to copy it
   - The address looks like: `0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb`

4. **Add to .env:**
   ```
   MERCHANT_ADDRESS=0xYourEthereumAddressHere
   MERCHANT_ADDRESS_BSC=0xYourBSCAddressHere
   MERCHANT_ADDRESS_POLYGON=0xYourPolygonAddressHere
   ```

   **Note:** You can use the same wallet address for all networks if your wallet supports multiple networks.

### 3. Network Configuration

Set which blockchain network to use by default:

```
CHAIN_NETWORK=ethereum
```

Options:
- `ethereum` - Ethereum Mainnet
- `bsc` - Binance Smart Chain
- `polygon` - Polygon Network

## Complete .env Configuration Example

```env
# Blockchain Network (ethereum, bsc, or polygon)
CHAIN_NETWORK=ethereum

# Blockchain Explorer API Keys
ETHERSCAN_API_KEY=YourEtherscanAPIKeyHere
BSCSCAN_API_KEY=YourBSCScanAPIKeyHere
POLYGONSCAN_API_KEY=YourPolygonScanAPIKeyHere

# Merchant Wallet Addresses 
MERCHANT_ADDRESS=0xYourEthereumWalletAddress
MERCHANT_ADDRESS_BSC=0xYourBSCWalletAddress
MERCHANT_ADDRESS_POLYGON=0xYourPolygonWalletAddress
```

## Setup Steps Summary

1. ✅ Get Etherscan API key (free) - https://etherscan.io/myapikey
2. ✅ Get BSCScan API key (free) - https://bscscan.com/myapikey  
3. ✅ Get PolygonScan API key (free) - https://polygonscan.com/myapikey
4. ✅ Create/import MetaMask wallet
5. ✅ Copy wallet addresses from MetaMask
6. ✅ Add all credentials to `.env` file
7. ✅ Restart your Laravel application

## Supported Tokens

### Native Tokens (No token address needed)
- **Ethereum:** ETH
- **BSC:** BNB
- **Polygon:** MATIC

### ERC20 Tokens (Require token contract address)
- **USDT** (Tether)
  - Ethereum: `0xdAC17F958D2ee523a2206206994597C13D831ec7`
  - BSC: `0x55d398326f99059fF775485246999027B3197955`
  - Polygon: `0xc2132D05D31c914a87C6611C10748AEb04B58e8F`

- **USDC** (USD Coin)
  - Ethereum: `0xA0b86991c6218b36c1d19D4a2e9Eb0cE3606eB48`
  - BSC: `0x8AC76a51cc950d9822D68b83fE1Ad97B32Cd580d`
  - Polygon: `0x2791Bca1f2de4661ED88A30C99A7a9449Aa84174`

## Testing

### Test Networks (for development)

You can use test networks for testing without spending real money:

- **Ethereum Testnet (Sepolia/Goerli):** Use Sepolia Etherscan API
- **BSC Testnet:** Use BSC Testnet Explorer
- **Polygon Testnet (Mumbai):** Use Mumbai PolygonScan

Update your `.env`:
```env
CHAIN_NETWORK=ethereum
ETHERSCAN_API_KEY=your_testnet_api_key
```

## Security Notes

⚠️ **IMPORTANT:**
- Never share your wallet seed phrase or private keys
- Keep your `.env` file secure and never commit it to version control
- Use different wallet addresses for production and testing
- Regularly backup your wallet
- Consider using a hardware wallet for large amounts

## Troubleshooting

### "API key not configured" error
- Make sure you've added the API key to `.env`
- Restart your Laravel application after adding credentials
- Check that the API key is correct (no extra spaces)

### "Transaction not found" error
- Transaction might still be pending (wait a few minutes)
- Check that you're using the correct network
- Verify the transaction hash is correct

### "Merchant address not configured" error
- Add `MERCHANT_ADDRESS` to your `.env` file
- Make sure the address is a valid Ethereum address format (starts with 0x)

## Support

For issues or questions:
1. Check the Laravel logs: `storage/logs/laravel.log`
2. Verify all credentials are correctly set in `.env`
3. Ensure your wallet has sufficient balance for gas fees
4. Check that the blockchain network is accessible

## Cost Information

- **API Keys:** FREE (no cost)
- **Wallet Creation:** FREE
- **Transaction Fees:** Users pay gas fees (varies by network)
  - Ethereum: ~$5-50 per transaction (high)
  - BSC: ~$0.10-1 per transaction (low)
  - Polygon: ~$0.01-0.10 per transaction (very low)

**Recommendation:** Use BSC or Polygon for lower transaction costs.

