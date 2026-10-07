#include <cassert>
#include <string>
#include "base64.h"
int main() {
    assert(base64_encode("abc") == "YWJj");
    assert(base64_decode("YWJj") == "abc");
    std::string bytes;
    for (int i = 0; i < 4096; ++i) bytes.push_back(static_cast<char>(i % 256));
    assert(base64_decode(base64_encode(bytes)) == bytes);
    assert(base64_encode("").empty());
}
