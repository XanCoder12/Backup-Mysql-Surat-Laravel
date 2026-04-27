#include <iostream>

using namespace std;

int main() {
  int t; 
  cout << "Masukkan jumlah test case: ";
  cin >> t;

  while (t--) {
    int n;
    cout << "Masukkan jumlah angka: ";
    cin >> n;

    int xor_result = 0; // hasil akhir
    for (int i = 0; i < n; i++) {
      int a;
      cout << "Angka ke-" << i + 1 << ": ";
      cin >> a;

      xor_result = xor_result ^ a; // simpan hasil XOR
    }

    cout << "Hasil XOR: " << xor_result << endl << endl;
  }

  return 0;
}